# Contributing to DokuWiki AI Assistant

DokuWiki AI Assistant 플러그인에 기여해주셔서 감사합니다! 이 문서는 프로젝트에 기여하는 방법을 안내합니다.

## 🤝 기여 방법

### 1. 이슈 리포트
버그를 발견하거나 새로운 기능을 제안하고 싶다면:

1. [Issues 페이지](https://github.com/yourusername/dokuwiki-aiassist/issues) 확인
2. 중복된 이슈가 없는지 검색
3. 새 이슈 생성 시 템플릿 사용

#### 버그 리포트 템플릿
```markdown
## 버그 설명
간단하고 명확한 버그 설명

## 재현 방법
1. '...' 으로 이동
2. '...' 클릭
3. '...' 입력
4. 오류 발생

## 예상 동작
정상적으로 작동해야 하는 방식

## 실제 동작
실제로 발생한 동작

## 환경 정보
- DokuWiki 버전: [예: 2023-04-04]
- PHP 버전: [예: 8.1]
- 브라우저: [예: Chrome 120]
- 플러그인 버전: [예: 1.2.0]

## 추가 정보
스크린샷, 로그 파일 등
```

### 2. 기능 요청
새로운 기능을 제안할 때:

```markdown
## 기능 설명
원하는 기능에 대한 명확한 설명

## 문제점
현재 어떤 문제가 있는지 설명

## 해결 방안
제안하는 해결 방법

## 대안
고려해본 다른 방법들

## 추가 정보
예시, 참고 자료 등
```

## 🔧 개발 환경 설정

### 1. 개발 환경 요구사항
```bash
# 필수 소프트웨어
- PHP 7.4+ (8.0+ 권장)
- DokuWiki 2023-04-04+
- Git
- 텍스트 에디터 (VS Code 권장)

# 선택사항
- Node.js (문서 빌드용)
- ImageMagick (아이콘 처리용)
```

### 2. 로컬 개발 설정
```bash
# 1. 포크 및 클론
git clone https://github.com/yourusername/dokuwiki-aiassist.git
cd dokuwiki-aiassist

# 2. DokuWiki 개발 환경에 링크
ln -s /path/to/dokuwiki-aiassist /path/to/dokuwiki/lib/plugins/aiassist

# 3. 개발용 설정
cp conf/default.php conf/local.php
# local.php에서 개발용 API 키 설정
```

### 3. 코딩 스타일

#### PHP 스타일 (PSR-12 준수)
```php
<?php
/**
 * 파일 설명
 * 
 * @author 작성자 <이메일>
 */

class ExampleClass 
{
    /**
     * 메서드 설명
     * 
     * @param string $param 매개변수 설명
     * @return bool 반환값 설명
     */
    public function exampleMethod($param)
    {
        if ($condition) {
            return true;
        }
        
        return false;
    }
}
```

#### JavaScript 스타일 (ES5 호환)
```javascript
/**
 * 함수 설명
 * @param {string} param - 매개변수 설명
 * @returns {boolean} 반환값 설명
 */
function exampleFunction(param) {
    if (condition) {
        return true;
    }
    
    return false;
}

// 변수 선언
var example = 'value';
var isValid = false;
```

#### CSS 스타일
```css
/* 컴포넌트별 분류 */
.aiassist-modal {
    /* 속성을 알파벳 순으로 정렬 */
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.aiassist-button {
    /* 상태별 스타일 */
    background: #0066cc;
    color: white;
}

.aiassist-button:hover {
    background: #0052a3;
}
```

## 📝 커밋 메시지 가이드

### Conventional Commits 사용
```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

#### 타입 종류
- `feat`: 새로운 기능
- `fix`: 버그 수정
- `docs`: 문서 변경
- `style`: 코드 포맷팅 (기능 변경 없음)
- `refactor`: 리팩토링
- `test`: 테스트 추가/수정
- `chore`: 빌드 프로세스, 도구 변경

#### 예시
```bash
feat: 요청사항 기능 추가

사용자가 "요청사항:내용" 형태로 맞춤 요청 가능
- extractCustomRequest() 메서드 추가
- buildPrompt() 로직 분기 처리
- 최종 결과에서 요청사항 부분 자동 제거

Closes #123
```

## 🔀 풀 리퀘스트 가이드

### 1. 브랜치 전략
```bash
# 기능 개발
git checkout -b feature/요청사항-기능
git checkout -b feature/toolbar-position

# 버그 수정
git checkout -b fix/api-key-validation
git checkout -b fix/utf8-encoding

# 문서 업데이트
git checkout -b docs/installation-guide
```

### 2. PR 체크리스트
- [ ] 코딩 스타일 준수
- [ ] 새로운 기능에 대한 테스트 추가
- [ ] 문서 업데이트 (필요한 경우)
- [ ] CHANGELOG.md 업데이트
- [ ] 설정 메타데이터 업데이트 (새 설정 추가 시)
- [ ] 언어 파일 업데이트 (UI 변경 시)

### 3. PR 템플릿
```markdown
## 변경 사항
이 PR에서 변경된 내용 요약

## 관련 이슈
Fixes #(이슈 번호)

## 변경 유형
- [ ] 버그 수정
- [ ] 새로운 기능
- [ ] 개선사항
- [ ] 문서 업데이트
- [ ] 기타

## 테스트
테스트 방법 및 결과

## 스크린샷
UI 변경이 있는 경우 스크린샷 첨부

## 체크리스트
- [ ] 코드가 프로젝트 스타일 가이드를 준수함
- [ ] 셀프 리뷰 완료
- [ ] 테스트 추가/업데이트
- [ ] 문서 업데이트
```

## 🧪 테스트 가이드

### 1. 수동 테스트
```bash
# 기본 기능 테스트
1. 텍스트 선택 → AI 버튼 클릭
2. 전체 텍스트 처리
3. 요청사항 기능 테스트
4. 오류 처리 테스트

# 브라우저 호환성 테스트
- Chrome (최신)
- Firefox (최신)
- Safari (최신)
- Edge (최신)
```

### 2. 설정 테스트
```bash
# 다양한 모델 테스트
- gpt-3.5-turbo
- gpt-4o-mini
- gpt-4o

# 다양한 설정 조합
- 최대 토큰: 1000, 4000, 8000
- 온도: 0.1, 0.3, 0.7
- 컨텍스트: 300, 500, 1000
```

## 📚 문서 기여

### 1. 문서 종류
- **README.md**: 기본 사용법 및 개요
- **INSTALL.md**: 상세 설치 가이드
- **CHANGELOG.md**: 버전별 변경사항
- **CONTRIBUTING.md**: 이 문서
- **lang/**: 다국어 지원

### 2. 문서 작성 가이드
- 명확하고 간결한 설명
- 코드 예시 포함
- 스크린샷 활용 (UI 설명 시)
- 다국어 지원 (한국어/영어)

## 🌐 다국어 지원

새로운 언어 추가 시:
```bash
# 1. 언어 디렉토리 생성
mkdir lang/ja  # 일본어 예시

# 2. 필수 파일 생성
touch lang/ja/lang.php
touch lang/ja/settings.php

# 3. 기존 파일을 참고하여 번역
cp lang/en/settings.php lang/ja/settings.php
# 내용 번역
```

## 📞 도움 요청

궁금한 점이나 도움이 필요하면:
1. [GitHub Discussions](https://github.com/yourusername/dokuwiki-aiassist/discussions)
2. [Issues 페이지](https://github.com/yourusername/dokuwiki-aiassist/issues)
3. 이메일: moontwt@example.com

## 🎯 개발 로드맵

### 단기 목표 (v1.3.0)
- [ ] 더 많은 AI 모델 지원
- [ ] 배치 처리 기능
- [ ] 커스텀 프롬프트 템플릿

### 중기 목표 (v2.0.0)
- [ ] 플러그인 아키텍처 개선
- [ ] 캐싱 시스템 추가
- [ ] 통계 및 모니터링 기능

### 장기 목표
- [ ] 다른 위키 시스템 지원
- [ ] 오픈소스 AI 모델 지원
- [ ] 협업 기능

---

**감사합니다!** 여러분의 기여가 이 프로젝트를 더욱 발전시킵니다. 🚀