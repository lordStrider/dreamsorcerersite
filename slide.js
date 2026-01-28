let slideIndex = 0; // Começa no primeiro slide
        showSlides();

        function showSlides() {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            let dots = document.getElementsByClassName("dot");
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slideIndex++;
            if (slideIndex > slides.length) {slideIndex = 1}
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex-1].style.display = "block";
            dots[slideIndex-1].className += " active";
            setTimeout(showSlides, 5000); // Muda de imagem a cada 5 segundos
        }

        // Funções para os botões manuais (opcional)
        function plusSlides(n) {
            clearTimeout(timeoutId); // Limpa o timer automático
            showManualSlides(slideIndex += n);
        }

        function currentSlide(n) {
            clearTimeout(timeoutId); // Limpa o timer automático
            showManualSlides(slideIndex = n);
        }

        function showManualSlides(n) {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            let dots = document.getElementsByClassName("dot");
            if (n > slides.length) {slideIndex = 1}
            if (n < 1) {slideIndex = slides.length}
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex-1].style.display = "block";
            dots[slideIndex-1].className += " active";
            // Reinicia o timer automático após a interação manual
            timeoutId = setTimeout(showSlides, 5000);
        }

        // Variável para armazenar o ID do timeout para poder limpá-lo
        let timeoutId;
        // Inicia o slideshow automático na primeira carga
        showSlides();