import SwiftUI

struct HomeView: View {
    @Environment(\.colorScheme) private var scheme

    private let driverName = "Marcus"
    private let delivered = 24
    private let pending = 8
    private let failed = 2
    private let pickups = 5
    private let cashCollected = 486.50
    private var total: Int { delivered + pending + failed }

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 16) {
                header
                CompletionRing(progress: Double(delivered) / Double(max(total, 1)), completed: delivered, total: total)
                CashCollectedCard(amount: cashCollected)
                statGrid
                quickActions
            }
            .padding(.horizontal, 18)
            .padding(.top, 8)
            .padding(.bottom, 100) // clears floating tab bar
        }
        .background(JibexTheme.canvas(scheme).ignoresSafeArea())
        .navigationBarHidden(true)
    }

    private var header: some View {
        HStack(alignment: .top) {
            VStack(alignment: .leading, spacing: 4) {
                Text("Good afternoon, \(driverName)")
                    .font(.system(.title2, design: .rounded).weight(.bold))
                    .foregroundStyle(JibexTheme.primaryText(scheme))
                HStack(spacing: 5) {
                    Image(systemName: "location.fill")
                        .font(.caption2)
                        .foregroundStyle(JibexTheme.brand)
                    Text("Route JX-4471 · Downtown Zone")
                        .font(.caption.weight(.medium))
                        .foregroundStyle(JibexTheme.secondaryText(scheme))
                }
            }
            Spacer()
            HStack(spacing: 10) {
                HeaderIconButton(systemImage: "qrcode.viewfinder")
                ZStack {
                    Image(systemName: "bell")
                        .foregroundStyle(JibexTheme.primaryText(scheme))
                        .font(.system(size: 16, weight: .medium))
                        .frame(width: 42, height: 42)
                        .liquidGlass(cornerRadius: 21, interactive: true)
                    Circle()
                        .fill(JibexTheme.failed)
                        .frame(width: 9, height: 9)
                        .overlay(Circle().stroke(JibexTheme.canvas(scheme), lineWidth: 2))
                        .offset(x: 14, y: -14)
                }
            }
        }
    }

    private var statGrid: some View {
        LazyVGrid(columns: [GridItem(.flexible()), GridItem(.flexible())], spacing: 12) {
            StatCard(icon: "checkmark.circle.fill", label: "Delivered", value: delivered, color: JibexTheme.delivered)
            StatCard(icon: "clock.fill", label: "Pending", value: pending, color: JibexTheme.pending)
            StatCard(icon: "xmark.octagon.fill", label: "Failed", value: failed, color: JibexTheme.failed)
            StatCard(icon: "shippingbox.fill", label: "Pickups", value: pickups, color: JibexTheme.pickup)
        }
    }

    private var quickActions: some View {
        VStack(alignment: .leading, spacing: 10) {
            Text("Quick Actions")
                .font(.subheadline.weight(.semibold))
                .foregroundStyle(JibexTheme.secondaryText(scheme))
                .padding(.leading, 2)

            QuickActionTile(icon: "list.bullet.rectangle.fill", title: "Runsheets", subtitle: "3 active routes", color: JibexTheme.brand)
            QuickActionTile(icon: "shippingbox.fill", title: "Pickups", subtitle: "\(pickups) requests waiting", color: JibexTheme.pickup)
            QuickActionTile(icon: "arrow.left.arrow.right", title: "Transfers", subtitle: "1 in progress", color: JibexTheme.pending)
            QuickActionTile(icon: "arrowshape.turn.up.left.fill", title: "Returns", subtitle: "No pending returns", color: JibexTheme.failed)
        }
    }
}
