import sys
import json
import os
import traceback
import logging

def main():
    if len(sys.argv) < 3:
        print(json.dumps({'success': False, 'error': 'Usage: tiktok_post.py <video_path> <description> [post_id]'}))
        sys.exit(1)

    video_path = sys.argv[1]
    description = sys.argv[2]
    post_id = sys.argv[3] if len(sys.argv) > 3 else None
    cookies_path = os.path.join(os.path.dirname(__file__), 'tiktok_cookies.txt')
    log_path = os.path.join(os.path.dirname(__file__), 'tiktok_post.log')

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

    log(f"=== Starting upload ===")
    log(f"Video: {video_path}")
    log(f"Description: {description}")
    log(f"Post ID: {post_id}")

    write_progress('starting', 5, 'Hazırlanıyor...')

    if not os.path.exists(video_path):
        msg = f'Video file not found: {video_path}'
        log(msg)
        write_progress('failed', 0, msg)
        print(json.dumps({'success': False, 'error': msg}))
        sys.exit(1)

    if not os.path.exists(cookies_path):
        msg = 'tiktok_cookies.txt not found'
        log(msg)
        write_progress('failed', 0, msg)
        print(json.dumps({'success': False, 'error': msg}))
        sys.exit(1)

    log(f"Video size: {os.path.getsize(video_path)} bytes")

    stage_map = [
        ('Authenticating',          'auth',        15, 'Kimlik dogrulaniyor...'),
        ('Create a chrome',         'browser',     20, 'Tarayici aciliyor...'),
        ('Navigating to upload',    'navigating',  35, 'Yukleme sayfasi aciliyor...'),
        ('Uploading video file',    'uploading',   55, 'Video yukleniyor...'),
        ('Setting interactivity',   'settings',    70, 'Ayarlar yapiliyor...'),
        ('Setting description',     'description', 80, 'Aciklama yaziliyor...'),
        ('Clicking the post',       'posting',     90, 'Post atiliyor...'),
        ('Video posted',            'completed',   98,  'Tamamlaniyor...'),
    ]

    class ProgressHandler(logging.Handler):
        def emit(self, record):
            msg = self.format(record)
            for marker, stage, percent, message in stage_map:
                if marker in msg:
                    write_progress(stage, percent, message)
                    log(f"[progress] {stage} {percent}% - {message}")
                    break

    # tiktok_uploader logger'ina kendi handler'imizi ekle
    tt_logger = logging.getLogger('tiktok_uploader')
    tt_logger.addHandler(ProgressHandler())

    try:
        from tiktok_uploader.upload import upload_video
        failed = upload_video(video_path, description=description, cookies=cookies_path)
        log(f"Result: {failed}")

        if failed and len(failed) > 0:
            write_progress('failed', 0, 'TikTok upload reddetti (cookieler suresi dolmus olabilir)')
            print(json.dumps({'success': False, 'error': f'TikTok upload failed: {failed}'}))
            sys.exit(1)

        write_progress('completed', 100, 'Yayinlandi!')
        print(json.dumps({'success': True}))
    except Exception as e:
        tb = traceback.format_exc()
        log(f"Exception: {e}\n{tb}")
        write_progress('failed', 0, str(e))
        print(json.dumps({'success': False, 'error': str(e)}))
        sys.exit(1)

if __name__ == '__main__':
    main()
