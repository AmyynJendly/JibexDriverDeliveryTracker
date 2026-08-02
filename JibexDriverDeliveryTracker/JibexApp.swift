import SwiftUI

@main
struct JibexApp: App {
    @State private var appearance = AppearanceManager()

    var body: some Scene {
        WindowGroup {
            RootTabView()
                .environment(appearance)
                .preferredColorScheme(appearance.preferredScheme)
        }
    }
}
