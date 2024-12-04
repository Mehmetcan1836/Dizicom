// "Yukarı Çık" butonunu kontrol etmek için
const scrollToTopBtn = document.getElementById("scrollToTopBtn");

// Sayfa kaydırıldığında butonu göster/gizle
window.addEventListener("scroll", () => {
    if (window.scrollY > 200) { // Eğer kaydırma 200px üzerindeyse
        scrollToTopBtn.style.display = "block";
    } else {
        scrollToTopBtn.style.display = "none";
    }
});

// Butona tıklandığında yumuşak kaydırma
scrollToTopBtn.addEventListener("click", () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});
// Scroll to top butonunun gösterilmesi
window.onscroll = function() {
    var scrollBtn = document.getElementById('scrollToTopBtn');
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        scrollBtn.style.display = "block";
    } else {
        scrollBtn.style.display = "none";
    }
};

// Butona tıklanınca sayfayı yukarı kaydırma
document.getElementById('scrollToTopBtn').onclick = function() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
};
