import sys
import json
import os
import re
import browser_cookie3
import requests

def try_browsers(domain='tiktok.com'):
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

def fetch_username(cookies):
    """Cookie'leri kullanarak TikTok'tan gerçek username'i çek."""
    cookies_dict = {c.name: c.value for c in cookies}
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36',
    }
    try:
        r = requests.get('https://www.tiktok.com/foryou', cookies=cookies_dict, headers=headers, timeout=10, allow_redirects=True)
        # uniqueId'yi HTML'den çek
        m = re.search(r'"uniqueId":"([^"]+)"', r.text)
        if m:
            return m.group(1)
        m = re.search(r'"username":"([^"]+)"', r.text)
        if m:
            return m.group(1)
    except Exception:
        pass
    return None

def main():
    cookies_path = os.path.join(os.path.dirname(__file__), 'tiktok_cookies.txt')

    browser, cookies, errors = try_browsers('tiktok.com')

    if not cookies:
        msg = 'Hiçbir browserde TikTok cookieleri bulunamadı. '
        if errors:
            msg += 'Hatalar: ' + ' | '.join(errors)
        print(json.dumps({'success': False, 'error': msg}))
        return

    has_session = any(c.name == 'sessionid' for c in cookies)
    if not has_session:
        print(json.dumps({'success': False, 'error': f'{browser} browserda TikTok\'a giriş yapılmamış.'}))
        return

    username = fetch_username(cookies) or 'tiktok_user'

    with open(cookies_path, 'w', encoding='utf-8') as f:
        f.write('# Netscape HTTP Cookie File\n')
        for c in cookies:
            domain = c.domain
            include_subdomains = 'TRUE' if domain.startswith('.') else 'FALSE'
            path = c.path or '/'
            secure = 'TRUE' if c.secure else 'FALSE'
            expires = int(c.expires) if c.expires else 0
            f.write(f"{domain}\t{include_subdomains}\t{path}\t{secure}\t{expires}\t{c.name}\t{c.value}\n")

    print(json.dumps({'success': True, 'username': username, 'browser': browser}))

if __name__ == '__main__':
    try:
        main()
    except Exception as e:
        print(json.dumps({'success': False, 'error': str(e)}))
