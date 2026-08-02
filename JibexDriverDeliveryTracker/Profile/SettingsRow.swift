import SwiftUI

/// A single row inside a settings section card — icon, label, and trailing control.
struct SettingsRow<Trailing: View>: View {
    let icon: String
    let iconColor: Color
    let title: String
    var subtitle: String? = nil
    @ViewBuilder var trailing: Trailing

    @Environment(\.colorScheme) private var scheme

    var body: some View {
        HStack(spacing: 14) {
            ZStack {
                RoundedRectangle(cornerRadius: 9, style: .continuous)
                    .fill(iconColor.opacity(0.12))
                    .frame(width: 30, height: 30)
                Image(systemName: icon)
                    .font(.system(size: 14, weight: .semibold))
                    .foregroundStyle(iconColor)
            }
            VStack(alignment: .leading, spacing: 2) {
                Text(title)
                    .font(.subheadline.weight(.medium))
                    .foregroundStyle(JibexTheme.primaryText(scheme))
                if let subtitle {
                    Text(subtitle)
                        .font(.caption2)
                        .foregroundStyle(JibexTheme.secondaryText(scheme))
                }
            }
            Spacer(minLength: 8)
            trailing
        }
        .frame(minHeight: 48)
    }
}

/// Groups related settings rows into one solid, opaque card with dividers.
struct SettingsSection<Content: View>: View {
    let title: String
    @ViewBuilder var content: Content

    @Environment(\.colorScheme) private var scheme

    var body: some View {
        VStack(alignment: .leading, spacing: 10) {
            Text(title.uppercased())
                .font(.caption.weight(.bold))
                .foregroundStyle(JibexTheme.secondaryText(scheme))
                .padding(.leading, 4)
                .tracking(0.5)

            VStack(spacing: 0) { content }
                .padding(.horizontal, 14)
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
}

/// Thin divider matched to the section card's inset padding.
struct SettingsRowDivider: View {
    @Environment(\.colorScheme) private var scheme
    var body: some View {
        Divider()
            .background(JibexTheme.secondaryText(scheme).opacity(0.12))
    }
}
