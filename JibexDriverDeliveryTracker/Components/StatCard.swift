import SwiftUI

struct StatCard: View {
    let icon: String
    let label: String
    let value: Int
    let color: Color

    @Environment(\.colorScheme) private var scheme

    var body: some View {
        VStack(alignment: .leading, spacing: 10) {
            ZStack {
                Circle()
                    .fill(color.opacity(0.12))
                    .frame(width: 34, height: 34)
                Image(systemName: icon)
                    .font(.system(size: 14, weight: .semibold))
                    .foregroundStyle(color)
            }
            Text("\(value)")
                .font(.system(size: 26, weight: .semibold, design: .rounded))
                .foregroundStyle(JibexTheme.primaryText(scheme))
            Text(label)
                .font(.footnote.weight(.medium))
                .foregroundStyle(JibexTheme.secondaryText(scheme))
        }
        .padding(16)
        .frame(maxWidth: .infinity, alignment: .leading)
        .background(
            RoundedRectangle(cornerRadius: JibexTheme.radiusMedium, style: .continuous)
                .fill(JibexTheme.cardSurface(scheme))
                .overlay(
                    RoundedRectangle(cornerRadius: JibexTheme.radiusMedium, style: .continuous)
                        .stroke(JibexTheme.hairline(scheme).opacity(0.5), lineWidth: 0.75)
                )
        )
    }
}
