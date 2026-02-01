import { sliderConfig } from "./sliderConfig.js";
export const slideTemplate = ()=> {
 
    const sliderPre = document.querySelector(".slider-pro");
    sliderPre.innerHTML = sliderConfig();

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
            autoplay: true,
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
