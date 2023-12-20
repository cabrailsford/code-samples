const shareBlock = document.querySelector('.wp-block-cab-share-this');
const shareCode = shareBlock.querySelector('.share-this__code');
const shareWeb = shareBlock.querySelector('.share-this__web');
const shareBtn = shareWeb.querySelector('.share-this__button');
const shareAlert = shareWeb.querySelector('.share-this__alert');
const shareData = {
	title: shareBtn.dataset.title,
	text: shareBtn.dataset.title,
	url: shareBtn.dataset.url
};

if ( navigator.share ) {
	shareCode.setAttribute('hidden', true);
	shareWeb.removeAttribute('hidden');
	shareBtn.addEventListener('click', async () => {
		try {
			await navigator.share(shareData);
			shareAlert.textContent = "Thank you!";
		} catch (err) {}
		setTimeout(() => {
			shareAlert.textContent = '';
		}, 3000);
	});
}
