import SwiftUI

/// One consistent loading/empty/error component reused across every list screen.
struct StatusEmptyStateView: View {
    enum Kind {
        case empty(icon: String, title: String, message: String)
        case error(message: String)
        case loading

        var icon: String {
            switch self {
            case .empty(let icon, _, _): return icon
            case .error: return "exclamationmark.triangle.fill"
            case .loading: return "arrow.triangle.2.circlepath"
            }
        }

        var title: String {
            switch self {
            case .empty(_, let title, _): return title
            case .error: return "Something went wrong"
            case .loading: return "Loading"
            }
        }

        var message: String {
            switch self {
            case .empty(_, _, let message): return message
            case .error(let message): return message
            case .loading: return "Fetching the latest data…"
            }
        }
    }

    let kind: Kind
    var retryAction: (() -> Void)? = nil

    @Environment(\.colorScheme) private var scheme

    var body: some View {
        VStack(spacing: 14) {
            ZStack {
                Circle()
                    .fill(JibexTheme.brand.opacity(0.12))
                    .frame(width: 72, height: 72)
                Image(systemName: kind.icon)
                    .font(.system(size: 30, weight: .semibold))
                    .foregroundStyle(JibexTheme.brand)
            }
            Text(kind.title)
                .font(.system(.headline, design: .rounded))
                .foregroundStyle(JibexTheme.primaryText(scheme))
            Text(kind.message)
                .font(.subheadline)
                .foregroundStyle(JibexTheme.secondaryText(scheme))
                .multilineTextAlignment(.center)
                .padding(.horizontal, 32)

            if let retryAction {
                Button(action: retryAction) {
                    Text("Try Again")
                        .font(.subheadline.weight(.semibold))
                        .padding(.horizontal, 20)
                        .padding(.vertical, 10)
                }
                .background(JibexTheme.brand)
                .foregroundStyle(.white)
                .clipShape(Capsule())
                .padding(.top, 4)
            }
        }
        .frame(maxWidth: .infinity)
        .padding(.vertical, 40)
    }
}
