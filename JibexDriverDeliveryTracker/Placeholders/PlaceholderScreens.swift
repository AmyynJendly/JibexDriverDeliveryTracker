import SwiftUI

/// Lightweight stand-ins for secondary tabs — real screens (segmented lists, etc.)
/// are out of scope for this pass, but the navigation shell feels complete.
struct SimpleScreenScaffold<Content: View>: View {
    let title: String
    var showScanner: Bool = false
    @ViewBuilder var content: Content

    @Environment(\.colorScheme) private var scheme

    var body: some View {
        NavigationStack {
            ScrollView {
                content
                    .padding(.horizontal, 18)
                    .padding(.top, 12)
                    .padding(.bottom, 120)
            }
            .background(JibexTheme.canvas(scheme).ignoresSafeArea())
            .navigationTitle(title)
            .toolbar {
                if showScanner {
                    ToolbarItem(placement: .topBarTrailing) {
                        HeaderIconButton(systemImage: "qrcode.viewfinder")
                    }
                }
            }
        }
    }
}

struct RunsheetsPlaceholderView: View {
    var body: some View {
        SimpleScreenScaffold(title: "Runsheets", showScanner: true) {
            StatusEmptyStateView(
                kind: .empty(
                    icon: "list.bullet.rectangle.fill",
                    title: "No Active Runsheets",
                    message: "Your assigned routes will show up here once dispatch sends them your way."
                ),
                retryAction: {}
            )
        }
    }
}

struct NotificationsPlaceholderView: View {
    var body: some View {
        SimpleScreenScaffold(title: "Notifications") {
            StatusEmptyStateView(
                kind: .empty(
                    icon: "bell.fill",
                    title: "You're All Caught Up",
                    message: "New delivery alerts and dispatch messages will appear here."
                )
            )
        }
    }
}
