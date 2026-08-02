# Jibex Driver Delivery Tracker

A native iOS delivery tracking app built with SwiftUI 6.0.

## Setup & Build

### On Windows (Development)

1. **Clone the repo locally:**
   ```bash
   git clone <your-repo-url>
   cd JibexDriverDeliveryTracker
   ```

2. **Edit code in VS Code or your editor**
   - All `.swift` files are in `JibexDriverDeliveryTracker/`
   - Project config is in `project.yml`

3. **Push to GitHub:**
   ```bash
   git add .
   git commit -m "Initial commit: Jibex iOS app"
   git push origin main
   ```

### Automated Build (GitHub Actions)

The project is configured to auto-build on every push via **GitHub Actions** running on macOS.

**To set up:**

1. **Create GitHub repo:**
   - Go to [GitHub](https://github.com/new)
   - Create a new repository
   - Push your local code:
     ```bash
     git remote add origin https://github.com/YOUR_USERNAME/JibexDriverDeliveryTracker.git
     git branch -M main
     git push -u origin main
     ```

2. **Monitor builds:**
   - Go to **Actions** tab in your GitHub repo
   - The workflow `.github/workflows/build-ios.yml` runs automatically
   - Check the build status and logs
   - Download build artifacts if needed

3. **Build triggers:**
   - Every push to `main` or `develop`
   - Every pull request to these branches

## Project Structure

```
JibexDriverDeliveryTracker/
├── JibexApp.swift                 # Entry point
├── RootTabView.swift              # Tab navigation
├── Home/
│   └── HomeView.swift             # Dashboard
├── Profile/
│   ├── ProfileView.swift
│   └── SettingsRow.swift
├── Placeholders/
│   └── PlaceholderScreens.swift   # Empty states
├── Components/
│   ├── CompletionRing.swift
│   ├── StatCard.swift
│   ├── CashCollectedCard.swift
│   ├── QuickActionTile.swift
│   ├── HeaderIconButton.swift
│   └── StatusEmptyStateView.swift
└── Theme/
    ├── JibexTheme.swift           # Colors & styles
    └── AppearanceManager.swift    # Dark mode
```

## Requirements

- **iOS 17.0+**
- **Swift 6.0**
- **Xcode 15.0+** (for building on Mac)

## Features

- **Home Dashboard**: Delivery stats, progress ring, cash collected
- **Profile**: Dark mode toggle, security settings
- **Floating Tab Bar**: Smooth glass-morphism UI
- **Empty States**: Loading, error, and empty placeholders
- **Theme Support**: Light/dark mode with persistent preference

## Development

### To modify the app:

1. Edit Swift files in VS Code on Windows
2. Commit and push to GitHub
3. **GitHub Actions automatically builds** on macOS
4. Check **Actions** tab for build success/failure
5. View logs or download build artifacts

### Local building (requires Mac):

```bash
xcodegen generate
open JibexDriverDeliveryTracker.xcodeproj
# Then in Xcode: Cmd+R to run
```

## Build Status

Check the **Actions** tab in your GitHub repository to see:
- Build pass/fail
- Build logs and errors
- Generated app artifacts

## License

Proprietary — Jibex Driver Delivery Tracker
