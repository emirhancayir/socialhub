import sys
import json
import os
import traceback
import time

def parse_netscape_cookies(path):
    cookies = []
    with open(path, 'r', encoding='utf-8') as f:
        for line in f:
            if line.startswith('#') or not line.strip():
                continue
            parts = line.strip().split('\t')
            if len(parts) < 7:
                continue
            domain, _, cpath, secure, expires, name, value = parts[:7]
            cookies.append({
                'name': name,
                'value': value,
                'domain': domain,
                'path': cpath,
                'secure': secure == 'TRUE',
                'expires': int(expires) if expires.isdigit() else -1,
            })
    return cookies

def main():
    if len(sys.argv) < 4:
        print(json.dumps({'success': False, 'error': 'Usage: instagram_post.py <username> <media_path> <caption> [post_id]'}))
        sys.exit(1)

    username   = sys.argv[1]
    media_path = sys.argv[2]
    caption    = sys.argv[3]
    post_id    = sys.argv[4] if len(sys.argv) > 4 else None

    log_path = os.path.join(os.path.dirname(__file__), 'instagram_post.log')
    cookies_path = os.path.join(os.path.dirname(__file__), 'instagram_cookies.txt')
    progress_dir = os.path.join(os.path.dirname(__file__), '..', 'storage', 'app', 'progress')
    os.makedirs(progress_dir, exist_ok=True)
    progress_path = os.path.join(progress_dir, f'post_{post_id}.json') if post_id else None

    def log(msg):
        with open(log_path, 'a', encoding='utf-8') as f:
            f.write(str(msg) + "\n")

    def write_progress(stage, percent, message):
        if not progress_path:
            return
        try:
            with open(progress_path, 'w', encoding='utf-8') as f:
                json.dump({'stage': stage, 'percent': percent, 'message': message}, f)
        except Exception:
            pass

    log("=== Instagram upload (Playwright) ===")
    log(f"User: {username}, Media: {media_path}, Post ID: {post_id}")
    write_progress('starting', 5, 'Hazirlaniyor...')

    if not os.path.exists(media_path):
        msg = f'Media not found: {media_path}'
        log(msg)
        write_progress('failed', 0, msg)
        print(json.dumps({'success': False, 'error': msg}))
        sys.exit(1)

    if not os.path.exists(cookies_path):
        msg = 'Instagram cookies bulunamadi. Once Bagla deyin.'
        log(msg)
        write_progress('failed', 0, msg)
        print(json.dumps({'success': False, 'error': msg}))
        sys.exit(1)

    try:
        from playwright.sync_api import sync_playwright

        write_progress('browser', 15, 'Tarayici aciliyor...')

        with sync_playwright() as p:
            browser = p.chromium.launch(headless=False)
            context = browser.new_context(
                user_agent='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36',
                viewport={'width': 1280, 'height': 800},
            )

            cookies = parse_netscape_cookies(cookies_path)
            valid_cookies = []
            for c in cookies:
                cookie = {
                    'name': c['name'],
                    'value': c['value'],
                    'domain': c['domain'],
                    'path': c['path'],
                    'secure': c['secure'],
                }
                if c['expires'] > 0:
                    cookie['expires'] = c['expires']
                valid_cookies.append(cookie)
            context.add_cookies(valid_cookies)

            page = context.new_page()
            write_progress('navigating', 25, 'Instagrama gidiliyor...')
            page.goto('https://www.instagram.com/', timeout=60000)
            time.sleep(3)

            log(f"URL after goto: {page.url}")

            def snap(name):
                p = os.path.join(os.path.dirname(__file__), f'ig_step_{post_id}_{name}.png')
                try:
                    page.screenshot(path=p)
                    log(f"snap: {p}")
                except Exception:
                    pass

            snap('01_home')

            # Create button (+ icon)
            write_progress('opening', 40, 'Yeni post aciliyor...')
            log("Looking for create button")
            try:
                page.locator('svg[aria-label="New post"]').first.click(timeout=15000)
            except Exception:
                # Yeni UI: Plus icon
                page.locator('a[href*="/create/"]').first.click(timeout=15000)
            time.sleep(2)

            # "Post" secenegi (eger menu acildiysa)
            try:
                page.get_by_role('menuitem', name='Post').click(timeout=5000)
            except Exception:
                pass

            time.sleep(2)
            snap('02_after_create_click')

            # File input
            write_progress('uploading', 60, 'Dosya yukleniyor...')
            log("Setting file input")
            file_input = page.locator('input[type="file"]').first
            file_input.set_input_files(media_path)

            time.sleep(8)
            snap('03_after_upload')

            # Next, Next (filtre, edit)
            for i in range(3):
                try:
                    btn = page.get_by_role('button', name='Next')
                    btn.click(timeout=10000)
                    time.sleep(3)
                    log(f"Clicked Next #{i+1}")
                    snap(f'04_after_next_{i+1}')
                except Exception as ex:
                    log(f"No Next button #{i+1}: {ex}")
                    break

            # Caption (multiple selector fallback)
            write_progress('caption', 80, 'Aciklama yaziliyor...')
            caption_selectors = [
                'div[aria-label="Write a caption..."][contenteditable="true"]',
                'div[role="textbox"][contenteditable="true"]',
                'textarea[aria-label="Write a caption..."]',
                'div[contenteditable="true"][data-lexical-editor="true"]',
            ]
            caption_set = False
            for sel in caption_selectors:
                try:
                    el = page.locator(sel).first
                    el.click(timeout=5000)
                    el.fill(caption)
                    log(f"Caption filled via {sel}")
                    caption_set = True
                    break
                except Exception as ex:
                    log(f"Caption selector failed [{sel}]: {ex}")
            if not caption_set:
                # Son care: keyboard ile yaz
                try:
                    page.keyboard.type(caption)
                    log("Caption typed via keyboard")
                except Exception as ex:
                    log(f"Keyboard type failed: {ex}")

            time.sleep(2)
            snap('05_after_caption')

            # Share button (multiple selectors)
            write_progress('posting', 90, 'Yayinlaniyor...')
            share_selectors = [
                ('role', 'Share'),
                ('role', 'Share to'),
                ('text', 'Share'),
                ('text', 'Paylas'),
            ]
            shared = False
            for kind, val in share_selectors:
                try:
                    if kind == 'role':
                        page.get_by_role('button', name=val).click(timeout=5000)
                    else:
                        page.get_by_text(val, exact=True).first.click(timeout=5000)
                    log(f"Clicked Share via {kind}={val}")
                    shared = True
                    break
                except Exception as ex:
                    log(f"Share selector failed [{kind}={val}]: {ex}")
            if not shared:
                raise Exception("Share button bulunamadi")

            # "Your post has been shared" mesajini bekle (max 5 dakika)
            log("Waiting for post completion...")
            completed = False
            for i in range(60):  # 60 * 5 = 300 sn
                time.sleep(5)
                # Sharing dialog kapandi mi?
                try:
                    sharing_visible = page.locator('text=/Sharing|Paylasiliyor/').first.is_visible(timeout=1000)
                except Exception:
                    sharing_visible = False

                # Basari mesaji?
                try:
                    success_visible = page.locator('text=/has been shared|paylasildi|Your post|Reel was shared/i').first.is_visible(timeout=1000)
                except Exception:
                    success_visible = False

                if success_visible:
                    log(f"Success message found at iteration {i}")
                    completed = True
                    break
                if not sharing_visible and i > 2:
                    log(f"Sharing dialog gone at iteration {i}")
                    completed = True
                    break

            # Debug screenshot
            ss_path = os.path.join(os.path.dirname(__file__), f'ig_debug_{post_id}.png')
            try:
                page.screenshot(path=ss_path, full_page=True)
                log(f"Screenshot saved: {ss_path}")
            except Exception:
                pass

            if not completed:
                raise Exception("Yayinlanma tamamlanmadi (timeout)")

            write_progress('completed', 100, 'Yayinlandi!')
            browser.close()

            print(json.dumps({'success': True}))
    except Exception as e:
        tb = traceback.format_exc()
        log(f"Exception: {e}\n{tb}")
        write_progress('failed', 0, str(e))
        print(json.dumps({'success': False, 'error': str(e)}))
        sys.exit(1)

if __name__ == '__main__':
    main()
