import SwiftUI

private enum Tab: Int, CaseIterable {
    case home, runsheets, notifications, profile

    var title: String {
        switch self {
        case .home: return "Home"
        case .runsheets: return "Runsheets"
        case .notifications: return "Alerts"
        case .profile: return "Profile"
        }
    }

    var icon: String {
        switch self {
        case .home: return "house.fill"
        case .runsheets: return "list.bullet.rectangle.fill"
        case .notifications: return "bell.fill"
        case .profile: return "person.fill"
        }
    }
}

struct RootTabView: View {
    @State private var selection: Tab = .home
    @Environment(\.colorScheme) private var scheme

    var body: some View {
        ZStack(alignment: .bottom) {
            Group {
                switch selection {
                case .home:
                    NavigationStack { HomeView() }
                case .runsheets:
                    RunsheetsPlaceholderView()
                case .notifications:
                    NotificationsPlaceholderView()
                case .profile:
                    ProfileView()
                }
            }

            floatingTabBar
                .padding(.horizontal, 28)
                .padding(.bottom, 10)
        }
    }

    private var floatingTabBar: some View {
        HStack(spacing: 0) {
            ForEach(Tab.allCases, id: \.rawValue) { tab in
                tabButton(tab)
            }
        }
        .padding(.vertical, 10)
        .padding(.horizontal, 8)
        .liquidGlass(cornerRadius: 34, interactive: true)
    }

    private func tabButton(_ tab: Tab) -> some View {
        let isSelected = selection == tab
        return Button {
            withAnimation(.snappy(duration: 0.25)) { selection = tab }
        } label: {
            VStack(spacing: 3) {
                Image(systemName: tab.icon)
                    .font(.system(size: 19, weight: isSelected ? .bold : .medium))
                Text(tab.title)
                    .font(.system(size: 10, weight: .semibold))
            }
            .foregroundStyle(isSelected ? JibexTheme.brand : JibexTheme.secondaryText(scheme))
            .frame(maxWidth: .infinity)
            .frame(height: 52)
            .background(
                Group {
                    if isSelected {
                        if #available(iOS 26.0, *) {
                            RoundedRectangle(cornerRadius: 20, style: .continuous)
                                .fill(Color.clear)
                                .glassEffect(.regular.tint(JibexTheme.brand.opacity(0.35)), in: RoundedRectangle(cornerRadius: 20, style: .continuous))
                        } else {
                            RoundedRectangle(cornerRadius: 20, style: .continuous)
                                .fill(JibexTheme.brand.opacity(0.14))
                        }
                    }
                }
            )
        }
        .buttonStyle(.plain)
    }
}
