import sys
import json
import os
import traceback
import browser_cookie3

def try_browsers(domain='instagram.com'):
    browsers = [
        ('chrome', browser_cookie3.chrome),
        ('edge', browser_cookie3.edge),
        ('firefox', browser_cookie3.firefox),
        ('brave', browser_cookie3.brave),
        ('opera', browser_cookie3.opera),
    ]
    errors = []
    for name, fn in browsers:
        try:
            cj = fn(domain_name=domain)
            cookies = list(cj)
            if cookies:
                return name, cookies, None
        except Exception as e:
            errors.append(f"{name}: {e}")
    return None, None, errors

def main():
    log_path = os.path.join(os.path.dirname(__file__), 'instagram_login.log')

    def log(msg):
        with open(log_path, 'a', encoding='utf-8') as f:
            f.write(str(msg) + "\n")

    log("=== Instagram login ===")

    browser, cookies, errors = try_browsers('instagram.com')

    if not cookies:
        msg = 'Instagram cookieleri bulunamadi.'
        if errors:
            msg += ' Hatalar: ' + ' | '.join(errors)
        log(msg)
        print(json.dumps({'success': False, 'error': msg}))
        return

    cookies_dict = {c.name: c.value for c in cookies}
    sessionid  = cookies_dict.get('sessionid')
    ds_user_id = cookies_dict.get('ds_user_id')

    if not sessionid or not ds_user_id:
        msg = f'{browser} browserda Instagrama giris yapilmamis.'
        log(msg)
        print(json.dumps({'success': False, 'error': msg}))
        return

    # Username'i instagrapi ile cek
    username = f'user_{ds_user_id}'
    full_name = username
    try:
        from instagrapi import Client
        cl = Client()
        cl.login_by_sessionid(sessionid)
        username = cl.username or username
    except Exception:
        pass

    # Cookieleri Netscape formatinda kaydet (Playwright icin)
    cookies_path = os.path.join(os.path.dirname(__file__), f'instagram_cookies.txt')
    with open(cookies_path, 'w', encoding='utf-8') as f:
        f.write('# Netscape HTTP Cookie File\n')
        for c in cookies:
            domain = c.domain
            include_subdomains = 'TRUE' if domain.startswith('.') else 'FALSE'
            path = c.path or '/'
            secure = 'TRUE' if c.secure else 'FALSE'
            expires = int(c.expires) if c.expires else 0
            f.write(f"{domain}\t{include_subdomains}\t{path}\t{secure}\t{expires}\t{c.name}\t{c.value}\n")

    log(f"Cookies saved for {username}")

    print(json.dumps({
        'success': True,
        'username': username,
        'full_name': full_name,
        'browser': browser,
    }))

if __name__ == '__main__':
    try:
        main()
    except Exception as e:
        print(json.dumps({'success': False, 'error': str(e)}))
