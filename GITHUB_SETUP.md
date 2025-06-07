# GitHub 리포지토리 설정 가이드

이 문서는 DokuWiki AI Assistant를 GitHub에 업로드하고 관리하는 방법을 설명합니다.

## 🚀 GitHub 리포지토리 생성

### 1. GitHub에서 새 리포지토리 생성
1. [GitHub](https://github.com)에 로그인
2. 우상단 "+" → "New repository" 클릭
3. 리포지토리 정보 입력:
   - **Repository name**: `dokuwiki-aiassist`
   - **Description**: `AI-powered text enhancement plugin for DokuWiki`
   - **Visibility**: ✅ Private (비공개)
   - **Initialize**: README, .gitignore, license 체크 해제 (이미 있음)

### 2. 로컬에서 Git 초기화
```bash
# 프로젝트 디렉토리로 이동
cd D:\project\aiassist

# Git 저장소 초기화
git init

# 첫 번째 커밋 준비
git add .
git commit -m "feat: 초기 DokuWiki AI Assistant 플러그인 v1.2.0

✨ 주요 기능:
- OpenAI API를 활용한 텍스트 개선
- 요청사항 기능 (요청사항:내용 형태)
- DokuWiki 문법 최적화
- 다단계 텍스트 검증
- 설정 가능한 컨텍스트 길이
- 툴바 버튼 위치 제어

📚 문서:
- 상세한 README.md 및 설치 가이드
- 기여자 가이드 및 이슈 템플릿
- 체인지로그 및 라이선스"

# 원격 저장소 연결
git remote add origin https://github.com/yourusername/dokuwiki-aiassist.git

# GitHub에 업로드
git branch -M main
git push -u origin main
```

## 📋 GitHub 설정

### 1. 리포지토리 설정
GitHub 리포지토리 페이지 → Settings에서:

#### General 설정
- **Default branch**: `main`
- **Features**: 
  - ✅ Issues
  - ✅ Projects  
  - ✅ Discussions (커뮤니티용)
  - ❌ Wiki (문서는 리포지토리 내에서 관리)

#### Branch protection
Settings → Branches → Add rule:
- **Branch name pattern**: `main`
- **Protect matching branches**:
  - ✅ Require pull request reviews before merging
  - ✅ Require status checks to pass before merging
  - ✅ Restrict pushes to matching branches

### 2. 이슈 라벨 설정
Issues → Labels에서 라벨 추가:

```
🐛 bug (버그) - #d73a49
✨ enhancement (기능 개선) - #a2eeef  
📚 documentation (문서) - #0075ca
❓ question (질문) - #d876e3
🔧 help wanted (도움 요청) - #008672
⚡ good first issue (첫 기여자용) - #7057ff
🚀 priority:high (높은 우선순위) - #e99695
📋 priority:medium (보통 우선순위) - #f9d71c
📝 priority:low (낮은 우선순위) - #c5def5
```

### 3. 프로젝트 보드 생성 (선택사항)
Projects → New project → Board:
- **To Do**: 계획된 작업
- **In Progress**: 진행 중인 작업  
- **Review**: 리뷰 중인 작업
- **Done**: 완료된 작업

## 🔐 보안 설정

### 1. API 키 보안
**중요**: 절대로 실제 OpenAI API 키를 리포지토리에 커밋하지 마세요!

```bash
# .gitignore에 이미 포함된 보안 파일들:
.env
.env.local
api-key.txt
```

### 2. 보안 정책 설정
Security → Security policy → Start setup:

```markdown
# Security Policy

## Supported Versions
| Version | Supported          |
| ------- | ------------------ |
| 1.2.x   | ✅ |
| 1.1.x   | ❌ |
| < 1.1   | ❌ |

## Reporting a Vulnerability
보안 취약점을 발견하면 moontwt@example.com으로 이메일을 보내주세요.
```

## 🏷️ 릴리즈 관리

### 1. 첫 번째 릴리즈 생성
Releases → Create a new release:
- **Tag version**: `v1.2.0`
- **Release title**: `v1.2.0 - 요청사항 기능 및 향상된 사용자 경험`
- **Description**: CHANGELOG.md의 1.2.0 내용 복사

### 2. 향후 릴리즈 프로세스
```bash
# 새 버전 개발 완료 후
git add .
git commit -m "feat: 새로운 기능 추가"

# 태그 생성
git tag v1.3.0
git push origin v1.3.0

# GitHub에서 릴리즈 생성
# Releases → Create a new release → Choose tag: v1.3.0
```

## 📊 Insights 및 분석

### 1. 커뮤니티 표준
Community → Community Standards에서 체크:
- ✅ Description
- ✅ README
- ✅ Code of conduct
- ✅ Contributing guidelines  
- ✅ License
- ✅ Issue templates
- ✅ Pull request template

### 2. 트래픽 모니터링
Insights → Traffic에서 확인:
- 클론 수
- 방문자 수
- 인기 콘텐츠

## 🤝 협업 설정

### 1. 팀 멤버 추가 (필요시)
Settings → Manage access → Invite a collaborator

### 2. 토론 활성화
Settings → Features → Discussions 체크
- **General**: 일반적인 질문과 토론
- **Ideas**: 새로운 아이디어 제안
- **Q&A**: 질문과 답변
- **Show and tell**: 사용 사례 공유

## 📱 모바일에서 관리

GitHub Mobile 앱을 설치하면:
- 이슈 알림 받기
- PR 리뷰하기
- 간단한 코드 편집

## 🔄 자동화 설정 (고급)

### 1. GitHub Actions (선택사항)
`.github/workflows/ci.yml` 생성:

```yaml
name: CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: PHP Syntax Check
        run: find . -name "*.php" -exec php -l {} \;
```

### 2. 자동 라벨링
Issues와 PR에 자동으로 라벨 추가하는 설정

## ✅ 설정 완료 체크리스트

- [ ] GitHub 리포지토리 생성 (Private)
- [ ] 로컬 Git 초기화 및 첫 커밋
- [ ] 원격 저장소 연결 및 푸시
- [ ] 브랜치 보호 설정
- [ ] 이슈 라벨 설정
- [ ] 보안 정책 설정
- [ ] 첫 번째 릴리즈 생성
- [ ] Community Standards 완성
- [ ] 토론 기능 활성화 (선택)

## 📞 추가 도움

- [GitHub Docs](https://docs.github.com/)
- [Git 기본 사용법](https://git-scm.com/doc)
- [Markdown 가이드](https://guides.github.com/features/mastering-markdown/)

---

🎉 **완료!** 이제 전문적인 오픈소스 프로젝트처럼 관리할 수 있습니다.