import SwiftUI

struct CompletionRing: View {
    let progress: Double // 0...1
    let completed: Int
    let total: Int

    @Environment(\.colorScheme) private var scheme
    @State private var animatedProgress: Double = 0

    var body: some View {
        VStack(alignment: .leading, spacing: 14) {
            HStack(alignment: .firstTextBaseline) {
                VStack(alignment: .leading, spacing: 4) {
                    Text("Today's Deliveries")
                        .font(.subheadline.weight(.semibold))
                        .foregroundStyle(JibexTheme.primaryText(scheme))
                    Text("\(completed) of \(total) completed")
                        .font(.caption)
                        .foregroundStyle(JibexTheme.secondaryText(scheme))
                }
                Spacer()
                Text("\(Int(progress * 100))%")
                    .font(.system(.title2, design: .rounded).weight(.bold))
                    .foregroundStyle(JibexTheme.brand)
            }

            GeometryReader { geo in
                ZStack(alignment: .leading) {
                    Capsule()
                        .fill(JibexTheme.subtleFill(scheme))

                    Capsule()
                        .fill(JibexTheme.brand)
                        .frame(width: geo.size.width * animatedProgress)
                }
            }
            .frame(height: 8)
            .onAppear {
                withAnimation(.easeOut(duration: 0.7)) { animatedProgress = progress }
            }

            HStack(spacing: 6) {
                Image(systemName: "clock")
                    .font(.caption2.weight(.semibold))
                    .foregroundStyle(JibexTheme.secondaryText(scheme))
                Text("On pace to finish by 5:30 PM")
                    .font(.caption2.weight(.medium))
                    .foregroundStyle(JibexTheme.secondaryText(scheme))
            }
        }
        .padding(18)
        .background(
            RoundedRectangle(cornerRadius: JibexTheme.radiusLarge, style: .continuous)
                .fill(JibexTheme.cardSurface(scheme))
                .overlay(
                    RoundedRectangle(cornerRadius: JibexTheme.radiusLarge, style: .continuous)
                        .stroke(JibexTheme.hairline(scheme).opacity(0.5), lineWidth: 0.75)
                )
        )
    }
}
