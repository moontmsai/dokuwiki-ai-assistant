# DokuWiki AI Assistant Plugin

An AI-powered text editing assistant plugin for DokuWiki. Uses OpenRouter API to access various AI models (GPT, Claude, Gemini, Mistral) to improve and optimize text with DokuWiki syntax.

## Key Features

- Automatically improve and optimize selected text with AI
- Automatic application of official DokuWiki syntax (lists, tables, links, etc.)
- Support for various AI models (GPT, Claude, Gemini, Mistral, etc.)
- Custom request processing ("request: content" or "요청사항: content" format)
- Real-time preview to check changes
- Complete preservation of original content and meaning

## System Requirements

- DokuWiki 2023-04-04 "Jack Jackrum" or higher
- PHP 7.4 or higher
- curl, json, mbstring extensions
- OpenRouter API key
- Internet connection

## Installation

### 1. Copy Files
```bash
cd /path/to/dokuwiki/lib/plugins/
git clone https://github.com/yourusername/dokuwiki-aiassist.git aiassist
```

### 2. Set Permissions
```bash
chown -R www-data:www-data aiassist/
chmod -R 755 aiassist/
```

### 3. Enable Plugin
1. Access DokuWiki admin page
2. Extension Manager > Extension Manager
3. Enable AI Assistant plugin

## Configuration

### 1. API Key Setup
1. Admin page > Configuration Manager > AI Assistant
2. Enter OpenRouter API key (get from openrouter.ai)
3. Select AI model (from dropdown)

### 2. Recommended Models
- **openrouter/auto**: Automatic optimal model selection (default)
- **mistralai/mistral-large-2411**: High-performance European model ($2.00/$6.00)
- **openai/gpt-4o-mini**: Economic choice ($0.15/$0.60)
- **anthropic/claude-sonnet-4**: Highest quality ($3.00/$15.00)
- **google/gemini-2.5-flash-preview-05-20**: Excellent value ($0.15/$0.60)

### 3. Other Settings
- **Max Tokens**: AI response length limit (default: 8000)
- **Temperature**: AI creativity level (default: 0.3)
- **Context Length**: Number of characters for context reference (default: 500)

## Usage

### 1. Basic Usage
1. Select text in DokuWiki edit mode
2. Click the AI button in toolbar
3. Check the result in preview
4. Click "Apply Changes"

### 2. Custom Request Feature
Add special requests at the end of text in "request: content" format:

```
Project progress status.
1. Planning completed
2. Development in progress
3. Testing scheduled

request: Convert to table format and use more professional tone
```

### 3. Automatic DokuWiki Syntax Optimization
- Convert numbered lists "1. item" → "  * item"
- Ensure consistent indentation
- Preserve existing syntax (**bold**, //italic//, [[links]], etc.)
- Correct spelling and spacing

## Pricing Information

| Model | Input (USD/1M tokens) | Output (USD/1M tokens) |
|-------|----------------------|------------------------|
| openrouter/auto | $0.10 | $0.10 |
| mistralai/mistral-large-2411 | $2.00 | $6.00 |
| openai/gpt-4o | $2.50 | $10.00 |
| openai/gpt-4o-mini | $0.15 | $0.60 |
| anthropic/claude-sonnet-4 | $3.00 | $15.00 |
| google/gemini-2.5-flash-preview | $0.15 | $0.60 |

## Troubleshooting

### API Key Error
- Check if OpenRouter API key is correctly configured
- Verify sufficient credits in API key

### AI Button Not Visible
- Check if plugin is enabled
- Verify in edit mode
- Clear browser cache

### Slow Response
- Use faster models (gpt-4o-mini, gemini-2.5-flash-preview)
- Reduce max token count
- Reduce context length

## License

MIT License

## Developer

**Author**: moontmsai  
**Email**: moontmsai@gmail.com

---

# DokuWiki AI Assistant 플러그인

DokuWiki용 AI 텍스트 편집 도우미 플러그인입니다. OpenRouter API를 통해 다양한 AI 모델을 사용하여 텍스트를 개선하고 DokuWiki 문법에 맞게 최적화합니다.

## 주요 기능

- 선택한 텍스트를 AI로 자동 개선 및 최적화
- DokuWiki 공식 문법 자동 적용 (목록, 표, 링크 등)
- 다양한 AI 모델 지원 (GPT, Claude, Gemini, Mistral 등)
- 맞춤형 요청사항 처리 ("요청사항: 내용" 형태)
- 실시간 미리보기로 변경사항 확인
- 원본 내용과 의미 완전 보존

## 시스템 요구사항

- DokuWiki 2023-04-04 "Jack Jackrum" 이상
- PHP 7.4 이상
- curl, json, mbstring 확장
- OpenRouter API 키
- 인터넷 연결

## 설치 방법

### 1. 파일 복사
```bash
cd /path/to/dokuwiki/lib/plugins/
git clone https://github.com/yourusername/dokuwiki-aiassist.git aiassist
```

### 2. 권한 설정
```bash
chown -R www-data:www-data aiassist/
chmod -R 755 aiassist/
```

### 3. 플러그인 활성화
1. DokuWiki 관리자 페이지 접속
2. 확장 관리 > 확장 관리자
3. AI Assistant 플러그인 활성화

## 설정 방법

### 1. API 키 설정
1. 관리자 페이지 > 설정 관리 > AI Assistant
2. OpenRouter API 키 입력 (openrouter.ai에서 발급)
3. AI 모델 선택 (드롭다운에서 선택)

### 2. 추천 모델 
- **openrouter/auto**: 자동 최적 모델 선택 (기본)
- **mistralai/mistral-large-2411**: 고성능 유럽 모델 ($2.00/$6.00)
- **openai/gpt-4o-mini**: 경제적 선택 ($0.15/$0.60)
- **anthropic/claude-sonnet-4**: 최고 품질 ($3.00/$15.00)
- **google/gemini-2.5-flash-preview-05-20**: 가성비 우수 ($0.15/$0.60)

### 3. 기타 설정
- **최대 토큰 수**: AI 응답 길이 제한 (기본: 8000)
- **온도**: AI 창의성 수준 (기본: 0.3)
- **컨텍스트 길이**: 앞뒤 참조 글자 수 (기본: 500)

## 사용 방법

### 1. 기본 사용
1. DokuWiki 편집 모드에서 텍스트 선택
2. 툴바의 AI 버튼 클릭
3. 미리보기에서 결과 확인
4. "변경사항 적용" 클릭

### 2. 요청사항 기능
텍스트 끝에 "요청사항: 내용" 형태로 특별 요청 추가:

```
프로젝트 진행 상황입니다.
1. 기획 완료
2. 개발 진행중
3. 테스트 예정

요청사항: 표 형태로 변경하고 더 전문적인 톤으로 수정해주세요
```

### 3. DokuWiki 문법 자동 최적화
- 번호 목록 "1. 항목" → "  * 항목"
- 들여쓰기 일관성 확보
- 기존 문법 보존 (**굵게**, //기울임//, [[링크]] 등)
- 맞춤법 및 띄어쓰기 교정

## 비용 정보

| 모델 | 입력 (USD/1M tokens) | 출력 (USD/1M tokens) |
|------|---------------------|---------------------|
| openrouter/auto | $0.10 | $0.10 |
| mistralai/mistral-large-2411 | $2.00 | $6.00 |
| openai/gpt-4o | $2.50 | $10.00 |
| openai/gpt-4o-mini | $0.15 | $0.60 |
| anthropic/claude-sonnet-4 | $3.00 | $15.00 |
| google/gemini-2.5-flash-preview | $0.15 | $0.60 |

## 문제 해결

### API 키 오류
- OpenRouter API 키가 올바르게 설정되었는지 확인
- API 키에 충분한 크레딧이 있는지 확인

### AI 버튼이 보이지 않음
- 플러그인이 활성화되었는지 확인
- 편집 모드에서 확인
- 브라우저 캐시 삭제

### 느린 응답
- 더 빠른 모델 사용 (gpt-4o-mini, gemini-2.5-flash-preview)
- 최대 토큰 수 줄이기
- 컨텍스트 길이 줄이기

## 라이선스

MIT License

## 개발자

**작성자**: moontmsai  
**이메일**: moontmsai@gmail.com