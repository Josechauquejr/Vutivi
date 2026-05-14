const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

const bindBannerParallax = (banner) => {
    const layers = [...banner.querySelectorAll("[data-parallax-depth]")];

    const resetLayers = () => {
        banner.style.setProperty("--spotlight-x", "78%");
        banner.style.setProperty("--spotlight-y", "24%");

        layers.forEach((layer) => {
            layer.style.setProperty("--parallax-x", "0px");
            layer.style.setProperty("--parallax-y", "0px");
        });
    };

    if (prefersReducedMotion.matches) {
        resetLayers();
        return;
    }

    let frameId = 0;

    const updateLayers = (clientX, clientY) => {
        const rect = banner.getBoundingClientRect();
        const offsetX = (clientX - rect.left) / rect.width - 0.5;
        const offsetY = (clientY - rect.top) / rect.height - 0.5;

        banner.style.setProperty("--spotlight-x", `${((offsetX + 0.5) * 100).toFixed(2)}%`);
        banner.style.setProperty("--spotlight-y", `${((offsetY + 0.5) * 100).toFixed(2)}%`);

        layers.forEach((layer) => {
            const depth = Number(layer.dataset.parallaxDepth || 0);

            layer.style.setProperty("--parallax-x", `${(-offsetX * depth).toFixed(2)}px`);
            layer.style.setProperty("--parallax-y", `${(-offsetY * depth).toFixed(2)}px`);
        });
    };

    banner.addEventListener("pointermove", (event) => {
        if (frameId) {
            cancelAnimationFrame(frameId);
        }

        frameId = requestAnimationFrame(() => {
            updateLayers(event.clientX, event.clientY);
        });
    });

    banner.addEventListener("pointerleave", () => {
        if (frameId) {
            cancelAnimationFrame(frameId);
        }

        resetLayers();
    });

    resetLayers();
};

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-banner-parallax]").forEach(bindBannerParallax);
});
