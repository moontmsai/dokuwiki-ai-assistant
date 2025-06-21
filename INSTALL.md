# DokuWiki AI Assistant - 설치 가이드

이 문서는 DokuWiki AI Assistant 플러그인의 상세한 설치 및 설정 가이드입니다.

## 📋 사전 준비사항

### 1. 시스템 요구사항 확인
```bash
# PHP 버전 확인 (7.4 이상 필요)
php -v

# 필요한 PHP 확장 모듈 확인
php -m | grep -E "(curl|json|mbstring)"

# DokuWiki 버전 확인
# 관리자 페이지 → 시스템 정보에서 확인
```

### 2. OpenAI API 키 준비
1. [OpenAI 웹사이트](https://platform.openai.com/)에 가입
2. API 키 생성: Dashboard → API Keys → Create new secret key
3. 키를 안전한 곳에 보관 (sk-로 시작하는 긴 문자열)

## 🚀 설치 방법

### 방법 1: Git 클론 (개발자 권장)

```bash
# DokuWiki 플러그인 디렉토리로 이동
cd /var/www/html/dokuwiki/lib/plugins/

# 리포지토리 클론
git clone https://github.com/yourusername/dokuwiki-aiassist.git aiassist

# 권한 설정
sudo chown -R www-data:www-data aiassist/
sudo chmod -R 755 aiassist/
```

### 방법 2: ZIP 다운로드 (일반 사용자)

```bash
# ZIP 파일 다운로드 후
cd /var/www/html/dokuwiki/lib/plugins/

# 압축 해제
unzip aiassist-main.zip
mv aiassist-main aiassist

# 권한 설정
sudo chown -R www-data:www-data aiassist/
sudo chmod -R 755 aiassist/
```

### 방법 3: 수동 업로드 (호스팅 서비스)

1. ZIP 파일을 다운로드
2. 압축 해제
3. FTP/SFTP로 `dokuwiki/lib/plugins/aiassist/` 폴더에 업로드
4. 웹 호스팅 제어판에서 권한 설정 (755)

## 🖼️ 아이콘 설정

### AI 버튼 아이콘 준비
```bash
# 방법 1: 제공된 SVG를 PNG로 변환
# (ImageMagick 사용)
convert ai-icon.svg -resize 16x16 images/ai-icon.png

# 방법 2: 직접 PNG 파일 제공
# 16x16 픽셀 PNG 파일을 images/ai-icon.png로 저장
```

## ⚙️ 플러그인 활성화

### 1. DokuWiki 관리자 접속
1. 브라우저에서 `yoursite.com/dokuwiki/doku.php?do=admin` 접속
2. 관리자 계정으로 로그인

### 2. 플러그인 활성화
1. **확장 관리** → **확장 관리자** 클릭
2. **AI Assistant** 찾기
3. **활성화** 체크박스 선택
4. **저장** 버튼 클릭

### 3. 설정 구성
1. **설정 관리** → **AI Assistant** 섹션 이동
2. 필수 설정 입력:
   - **OpenAI API 키**: 준비한 API 키 입력
   - **모델 선택**: 추천은 `gpt-4o-mini` (경제적)
3. **저장** 클릭

## 🔧 고급 설정

### 1. 상세 설정 옵션

#### 성능 최적화 설정
```
최대 토큰 수: 2000-4000 (일반적인 사용)
온도: 0.3 (일관성 중시) ~ 0.7 (창의성 중시)
컨텍스트 길이: 500 (기본값, 적당한 성능)
```

#### 비용 최적화 설정
```
모델: gpt-4o-mini (가장 경제적)
최대 토큰 수: 1000-2000 (비용 절약)
컨텍스트 길이: 300 (트래픽 절약)
```

#### 품질 중시 설정
```
모델: gpt-4o (최고 품질)
최대 토큰 수: 4000-8000 (상세한 응답)
컨텍스트 길이: 1000 (더 넓은 문맥)
```

### 2. 툴바 위치 조정
```
툴바 우선순위: 9999 (기본값, 가장 끝)
다른 플러그인보다 뒤에 위치시키려면: 더 큰 값 (예: 99999)
앞에 위치시키려면: 작은 값 (예: 100)
```

## 🧪 설치 테스트

### 1. 기본 기능 테스트
```
1. 새 페이지 편집 모드 진입
2. 툴바에 AI 버튼(🤖) 확인
3. 테스트 텍스트 입력: "안녕하세요. 이것은 테스트입니다."
4. 텍스트 선택 후 AI 버튼 클릭
5. 로딩 메시지 → 미리보기 → 적용 확인
```

### 2. 요청사항 기능 테스트
```
테스트 입력:
안녕하세요.
1. 첫번째 항목
2. 두번째 항목

요청사항: 도쿠위키 목록 문법으로 변경해주세요

결과 확인:
- "요청사항:" 부분이 제거됨
- 목록이 "  * 첫번째 항목" 형태로 변환됨
```

### 3. 오류 처리 테스트
```
1. 빈 텍스트 선택 시 → "처리할 텍스트가 없습니다" 메시지
2. 잘못된 API 키 → "OpenAI API key not configured" 오류
3. 네트워크 오류 → "서버 요청이 실패했습니다" 메시지
```

## 🛠️ 문제 해결

### Apache 환경 설정
```apache
# .htaccess에 추가 (필요한 경우)
<IfModule mod_rewrite.c>
    RewriteEngine On
    # AI Assistant AJAX 요청 허용
    RewriteRule ^lib/plugins/aiassist/ - [L]
</IfModule>
```

### Nginx 환경 설정
```nginx
# nginx.conf에 추가 (필요한 경우)
location ~* ^/lib/plugins/aiassist/ {
    # AI Assistant 플러그인 파일 접근 허용
    allow all;
}
```

### PHP 설정 확인
```php
# php.ini 설정 확인
max_execution_time = 300        # API 호출을 위한 충분한 시간
max_input_vars = 3000          # 폼 데이터 처리
post_max_size = 50M            # POST 데이터 크기
upload_max_filesize = 50M      # 업로드 파일 크기
```

### 권한 문제 해결
```bash
# 모든 파일 권한 재설정
sudo find aiassist/ -type f -exec chmod 644 {} \;
sudo find aiassist/ -type d -exec chmod 755 {} \;
sudo chown -R www-data:www-data aiassist/
```

## 🔍 로그 및 디버깅

### 1. DokuWiki 디버그 모드 활성화
```php
# conf/local.php에 추가
$conf['allowdebug'] = 1;
```

### 2. 로그 파일 위치
```bash
# DokuWiki 로그
tail -f data/cache/debug.log

# 웹서버 로그
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx

# PHP 로그
tail -f /var/log/php_errors.log
```

### 3. JavaScript 디버깅
```javascript
// 브라우저 콘솔에서 실행
console.log('AI Assistant 설정:', {
    contextLength: window.AIASSIST_CONTEXT_LENGTH,
    initialized: window.aiAssistantInitialized
});
```

## 📊 성능 모니터링

### OpenAI API 사용량 확인
1. [OpenAI Dashboard](https://platform.openai.com/usage) 접속
2. 사용량 및 비용 확인
3. 사용량 제한 설정 (권장)

### DokuWiki 성능 확인
```bash
# 접속 로그 분석 (Apache)
grep "aiassist" /var/log/apache2/access.log

# 응답 시간 모니터링
tail -f /var/log/apache2/access.log | grep "POST.*doku.php"
```

## 🚀 업그레이드

### Git을 통한 업그레이드
```bash
cd /var/www/html/dokuwiki/lib/plugins/aiassist/
git pull origin main
sudo chown -R www-data:www-data .
```

### 수동 업그레이드
1. 새 버전 다운로드
2. 기존 파일 백업
3. 새 파일로 교체 (conf/ 디렉토리는 보존)
4. 권한 재설정

## ✅ 설치 완료 체크리스트

- [ ] PHP 요구사항 확인 (7.4+, curl, json, mbstring)
- [ ] DokuWiki 버전 확인 (2023-04-04+)
- [ ] 플러그인 파일 업로드
- [ ] 권한 설정 (755/644)
- [ ] AI 아이콘 파일 준비 (16x16 PNG)
- [ ] 플러그인 활성화
- [ ] OpenAI API 키 설정
- [ ] 모델 및 성능 설정
- [ ] 기본 기능 테스트
- [ ] 요청사항 기능 테스트
- [ ] 오류 처리 테스트
- [ ] 성능 모니터링 설정

## 📞 지원

설치 중 문제가 발생하면:
1. 이 문서의 문제 해결 섹션 확인
2. [GitHub Issues](https://github.com/yourusername/dokuwiki-aiassist/issues) 검색
3. 새로운 이슈 등록 (로그 파일 포함)

---

🎉 **설치 완료!** 이제 AI의 도움으로 더 나은 문서를 작성하세요!