import sys
import json
import os
import traceback
import time

def main():
    if len(sys.argv) < 3:
        print(json.dumps({'success': False, 'error': 'Usage: twitter_post.py <text> <media_path_or_empty> [post_id]'}))
        sys.exit(1)

    text       = sys.argv[1]
    media_path = sys.argv[2] if len(sys.argv) > 2 and sys.argv[2] else None
    post_id    = sys.argv[3] if len(sys.argv) > 3 else None

    log_path     = os.path.join(os.path.dirname(__file__), 'twitter_post.log')
    cookies_path = os.path.join(os.path.dirname(__file__), 'twitter_cookies.json')
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

    log("=== Twitter post (Playwright) ===")
    log(f"Text: {text[:60]}, Media: {media_path}, Post ID: {post_id}")
    write_progress('starting', 5, 'Hazirlaniyor...')

    if not os.path.exists(cookies_path):
        msg = 'Twitter cookies bulunamadi. Once Bagla deyin.'
        log(msg)
        write_progress('failed', 0, msg)
        print(json.dumps({'success': False, 'error': msg}))
        sys.exit(1)

    with open(cookies_path, 'r', encoding='utf-8') as f:
        cookies_dict = json.load(f)

    try:
        from playwright.sync_api import sync_playwright

        write_progress('browser', 15, 'Tarayici aciliyor...')

        with sync_playwright() as p:
            browser = p.chromium.launch(headless=False)
            context = browser.new_context(
                user_agent='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36'
            )
            context.add_cookies([
                {'name': k, 'value': v, 'domain': '.x.com', 'path': '/'}
                for k, v in cookies_dict.items() if v
            ])

            page = context.new_page()
            write_progress('navigating', 25, 'Twitter aciliyor...')
            page.goto('https://x.com/home', timeout=30000)
            time.sleep(3)
            log(f"URL: {page.url}")

            def snap(name):
                p = os.path.join(os.path.dirname(__file__), f'tw_step_{post_id}_{name}.png')
                try:
                    page.screenshot(path=p)
                    log(f"snap: {p}")
                except Exception:
                    pass

            snap('01_home')

            # Once "What's happening?" alanina tikla (genislemesi icin)
            write_progress('composing', 45, 'Tweet yaziliyor...')
            try:
                page.locator('div[data-testid="tweetTextarea_0"]').first.click(timeout=10000)
                log("Clicked compose area")
            except Exception as ex:
                log(f"Compose area click failed: {ex}")
                # Fallback: placeholder tikla
                try:
                    page.get_by_placeholder("What's happening?").first.click(timeout=5000)
                except Exception:
                    page.locator('div[role="textbox"]').first.click(timeout=5000)
            time.sleep(1)

            # Slate.js editoru icin keyboard.type() kullan (fill() calismiyor)
            page.keyboard.type(text, delay=30)
            log(f"Text typed: {text[:40]}")
            time.sleep(2)
            snap('02_after_text')

            # Medya yükle
            if media_path and os.path.exists(media_path):
                write_progress('uploading', 60, 'Medya yukleniyor...')
                file_input = page.locator('input[data-testid="fileInput"]').first
                file_input.set_input_files(media_path)
                time.sleep(5)
                log("Media uploaded")

            # Post - butona tiklamadan once enabled olmasini bekle
            write_progress('posting', 85, 'Yayinlaniyor...')
            post_btn = page.locator('button[data-testid="tweetButtonInline"]').first
            for _ in range(30):
                try:
                    if not post_btn.is_disabled(timeout=1000):
                        break
                except Exception:
                    pass
                time.sleep(0.5)
            post_btn.click(timeout=10000)
            log("Clicked post button")
            time.sleep(5)

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
