export const sliderConfig = ()=> {
    let mySlider = "";
    const sliderContent = [
        {
            "title": "BRAVE DREAM GUARDIAN",
            "logoTitle": "images/logo-brave.png",
            "image": "img/brave-dream-slide.jpg",
            "description": "<span>Divirta-se viajando pelo mundo dos sonhos.</span>",
        },
        {
            "title": "ZEN NOW",
            "logoTitle": "images/logo-zennow.png",
            "image": "img/zennowslide.jpg",
            "description": "<span>Relaxe e medite ao som da natureza e sons relaxantes.</span>",
        },
        {
            "title": "BIBLIA QUIZ",
            "logoTitle": "images/logo-biblequiz.png",
            "image": "img/bibliaquiz-slide.jpg",
            "description": "<span>Relaxe e divirta-se mostrando seus conhecimentos Bíblicos</span>",
        },
    ]
    sliderContent.forEach((e, index) => {
        mySlider += `
        <!-- Slide ${index + 1} -->
        <div class="sp-slide">
            <div class="logo-mark"><img src="${e.logoTitle}"><p> ${e.title}</p></div>
            <p class="sp-layer"><span>${e.description}</span></p>
            <img class="sp-image" src="${e.image}">
        </div>
        `;
    });
    let criandoSlider = `
        <div class="sp-slides">
        ${mySlider}
        </div>
    `; 
    return criandoSlider;
}