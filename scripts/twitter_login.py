import sys
import json
import os
import traceback
import time

def main():
    log_path = os.path.join(os.path.dirname(__file__), 'twitter_login.log')
    cookies_path = os.path.join(os.path.dirname(__file__), 'twitter_cookies.json')

    def log(msg):
        with open(log_path, 'a', encoding='utf-8') as f:
            f.write(str(msg) + "\n")

    log("=== Twitter login (Playwright) ===")

    try:
        from playwright.sync_api import sync_playwright

        with sync_playwright() as p:
            browser = p.chromium.launch(headless=False)
            context = browser.new_context()
            page = context.new_page()
            page.goto('https://x.com/login', timeout=30000)

            log("Opened Twitter login page, waiting for user to log in...")

            # Kullanici giris yapana kadar bekle (auth_token cookie olusuncaya kadar)
            for _ in range(300):  # 5 dakika
                cookies = {c['name']: c['value'] for c in context.cookies()}
                if cookies.get('auth_token'):
                    log("auth_token found, logged in!")
                    break
                time.sleep(1)

            cookies = {c['name']: c['value'] for c in context.cookies()}
            if not cookies.get('auth_token'):
                browser.close()
                print(json.dumps({'success': False, 'error': 'Giris yapilmadi (timeout)'}))
                return

            # Username al
            username = 'twitter_user'
            try:
                page.goto('https://x.com/home', timeout=15000)
                time.sleep(2)
                el = page.locator('a[data-testid="AppTabBar_Profile_Link"]').first
                href = el.get_attribute('href', timeout=5000)
                if href:
                    username = href.lstrip('/')
            except Exception as ex:
                log(f"Username fetch failed: {ex}")

            with open(cookies_path, 'w', encoding='utf-8') as f:
                json.dump(cookies, f)

            browser.close()
            log(f"Saved cookies for @{username}")

            print(json.dumps({'success': True, 'username': username, 'full_name': username}))

    except Exception as e:
        tb = traceback.format_exc()
        log(f"Exception: {e}\n{tb}")
        print(json.dumps({'success': False, 'error': str(e)}))

if __name__ == '__main__':
    try:
        main()
    except Exception as e:
        print(json.dumps({'success': False, 'error': str(e)}))
