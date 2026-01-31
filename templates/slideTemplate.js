export const slideTemplate = ()=> {
  const template =  `<div class="sp-slides">
        <!-- Slide 1 -->
        <div class="sp-slide">
        <div class="logo-mark"><img src="images/logo-brave.png"></div>
            <p class="sp-layer"><span>Divirta-se viajando pelo mundo dos sonhos.</span></p>
            <img class="sp-image" src="img/brave-dream-slide.jpg">
        </div>

        <!-- Slide 2 -->d
        <div class="sp-slide">
            <img class="sp-image" src="img/zennowslide.jpg">
        </div>

        <!-- Slide 3 -->
        <div class="sp-slide">
            <img class="sp-image" src="img/bibliaquiz-slide.jpg">
            <h3 class="sp-layer">Lorem ipsum dolor sit amet</h3>
        </div>
    </div>   
`;
    const sliderPre = document.querySelector(".slider-pro");
    sliderPre.innerHTML = template;

    const slider = new SliderPro('#my-slider', {
            autoplayDirection: 'normal',
            width: "100vw",
            height: "65vh",
            fade: true,
            arrows: true,
            buttons: false,
            waitForLayers: true,
            thumbnailWidth: 200,
            thumbnailHeight: 100,
            thumbnailPointer: true,
            autoplay: false,
            autoplayDelay: 3000,
            fade: true,
            fadeDuration: 1500,
            autoScaleLayers: true,
            breakpoints: {
                500: {
                    thumbnailWidth: 120,
                    thumbnailHeight: 50
                }
            }
        });
}
