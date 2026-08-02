import SwiftUI

struct QuickActionTile: View {
    let icon: String
    let title: String
    let subtitle: String
    let color: Color

    @Environment(\.colorScheme) private var scheme

    var body: some View {
        HStack(spacing: 14) {
            ZStack {
                Circle()
                    .fill(color.opacity(0.12))
                    .frame(width: 40, height: 40)
                Image(systemName: icon)
                    .font(.system(size: 17, weight: .semibold))
                    .foregroundStyle(color)
            }
            VStack(alignment: .leading, spacing: 2) {
                Text(title)
                    .font(.subheadline.weight(.semibold))
                    .foregroundStyle(JibexTheme.primaryText(scheme))
                Text(subtitle)
                    .font(.caption)
                    .foregroundStyle(JibexTheme.secondaryText(scheme))
            }
            Spacer(minLength: 0)
            Image(systemName: "chevron.right")
                .font(.system(size: 13, weight: .semibold))
                .foregroundStyle(JibexTheme.secondaryText(scheme).opacity(0.6))
        }
        .padding(14)
        .frame(minHeight: 72)
        .background(
            RoundedRectangle(cornerRadius: JibexTheme.radiusMedium, style: .continuous)
                .fill(JibexTheme.cardSurface(scheme))
                .overlay(
                    RoundedRectangle(cornerRadius: JibexTheme.radiusMedium, style: .continuous)
                        .stroke(JibexTheme.hairline(scheme).opacity(0.5), lineWidth: 0.75)
                )
        )
        .contentShape(RoundedRectangle(cornerRadius: JibexTheme.radiusMedium, style: .continuous))
    }
}
