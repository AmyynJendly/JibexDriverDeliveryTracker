import SwiftUI

/// Drives the app's in-app "Appearance" preference (Profile > Appearance),
/// independent of the system setting. Persisted across launches.
@Observable
final class AppearanceManager {
    var isDarkMode: Bool {
        didSet { UserDefaults.standard.set(isDarkMode, forKey: Self.key) }
    }

    private static let key = "jibex.appearance.isDarkMode"

    init() {
        if UserDefaults.standard.object(forKey: Self.key) != nil {
            isDarkMode = UserDefaults.standard.bool(forKey: Self.key)
        } else {
            // Default to following the system on first launch.
            isDarkMode = UITraitCollection.current.userInterfaceStyle == .dark
        }
    }

    var preferredScheme: ColorScheme { isDarkMode ? .dark : .light }
}
