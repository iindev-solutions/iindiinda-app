export function shouldShowUiBackButton(isInTelegram: boolean, forceUi: boolean): boolean {
	return !isInTelegram || forceUi
}

export function shouldShowTelegramBackButton(isInTelegram: boolean): boolean {
	return isInTelegram
}
