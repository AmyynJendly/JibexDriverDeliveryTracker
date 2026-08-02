import SwiftUI

/// Callout card showing the driver's total cash collected (COD) for the day.
/// Matches the existing stat card styling — same corner radius, surface, and hairline stroke.
struct CashCollectedCard: View {
    let amount: Double

    @Environment(\.colorScheme) private var scheme

    private var formattedAmount: String {
        let formatter = NumberFormatter()
        formatter.numberStyle = .decimal
        formatter.minimumFractionDigits = 2
        formatter.maximumFractionDigits = 2
        let number = formatter.string(from: NSNumber(value: amount)) ?? "0.00"
        return "\(number) DT"
    }

    var body: some View {
        HStack(spacing: 12) {
            ZStack {
                Circle()
                    .fill(JibexTheme.delivered.opacity(0.12))
                    .frame(width: 36, height: 36)
                Image(systemName: "banknote.fill")
                    .font(.system(size: 15, weight: .semibold))
                    .foregroundStyle(JibexTheme.delivered)
            }
            Text("Cash Collected")
                .font(.subheadline.weight(.medium))
                .foregroundStyle(JibexTheme.secondaryText(scheme))
            Spacer()
            Text(formattedAmount)
                .font(.system(size: 20, weight: .bold, design: .rounded))
                .foregroundStyle(JibexTheme.primaryText(scheme))
        }
        .padding(.vertical, 12)
        .padding(.horizontal, 16)
        .frame(maxWidth: .infinity)
        .liquidGlass(cornerRadius: JibexTheme.radiusLarge, tint: JibexTheme.delivered)
    }
}
