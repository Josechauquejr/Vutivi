const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

const showToast = (message, type = "success") => {
    let region = document.querySelector("[data-toast-region]");

    if (!region) {
        region = document.createElement("div");
        region.dataset.toastRegion = "";
        region.className = "fixed right-4 top-24 z-50 grid w-[min(24rem,calc(100vw-2rem))] gap-3";
        document.body.appendChild(region);
    }

    const palette = {
        success: "border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-100",
        error: "border-red-200 bg-red-50 text-red-900 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-100",
        warning: "border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-100",
        info: "border-sky-200 bg-sky-50 text-sky-900 dark:border-sky-900/50 dark:bg-sky-950/30 dark:text-sky-100",
    };

    const toast = document.createElement("div");
    toast.dataset.toast = "";
    toast.className = `${palette[type] || palette.info} flex items-start gap-3 rounded-2xl border p-4 shadow-[0_18px_48px_rgba(54,39,25,0.16)]`;
    toast.innerHTML = `
        <span class="mt-0.5 flex h-8 w-8 flex-none items-center justify-center rounded-xl bg-white/70 dark:bg-black/25">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="${type === "error" ? "M18 6 6 18M6 6l12 12" : "M20 6 9 17l-5-5"}"/></svg>
        </span>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-black">${type === "error" ? "Atenção" : type === "warning" ? "Alerta" : "Notificação"}</p>
            <p class="mt-1 text-sm leading-5">${message}</p>
        </div>
        <button type="button" data-toast-close class="rounded-lg p-1 opacity-70 hover:bg-black/5 hover:opacity-100 dark:hover:bg-white/10">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
    `;
    region.appendChild(toast);

    const dismiss = () => {
        toast.classList.add("is-leaving");
        setTimeout(() => toast.remove(), 220);
    };

    toast.querySelector("[data-toast-close]")?.addEventListener("click", dismiss);
    setTimeout(dismiss, 5200);
};

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

    document.querySelectorAll(".navbar-search").forEach((search) => {
        const input = search.querySelector("[data-smart-search]");
        const panel = search.querySelector("[data-search-panel]");

        if (!input || !panel) {
            return;
        }

        const open = () => {
            panel.classList.remove("invisible", "opacity-0");
            panel.classList.add("opacity-100");
        };

        const close = () => {
            panel.classList.add("invisible", "opacity-0");
            panel.classList.remove("opacity-100");
        };

        input.addEventListener("focus", open);
        input.addEventListener("input", open);
        document.addEventListener("pointerdown", (event) => {
            if (!search.contains(event.target)) {
                close();
            }
        });
    });

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.16 },
    );

    document.querySelectorAll("[data-reveal]").forEach((element) => observer.observe(element));

    document.querySelectorAll("[data-upload-input]").forEach((input) => {
        const zone = input.closest("[data-upload-zone]");
        const preview = document.querySelector(input.dataset.previewTarget);
        const previewImage = preview?.querySelector("img");

        const showFile = (file) => {
            if (!file || !previewImage || !file.type.startsWith("image/")) {
                return;
            }

            preview.classList.remove("skeleton");
            previewImage.src = URL.createObjectURL(file);
            previewImage.classList.remove("hidden");
        };

        input.addEventListener("change", () => showFile(input.files?.[0]));

        if (zone) {
            ["dragenter", "dragover"].forEach((eventName) => {
                zone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    zone.classList.add("is-dragging");
                });
            });

            ["dragleave", "drop"].forEach((eventName) => {
                zone.addEventListener(eventName, () => zone.classList.remove("is-dragging"));
            });

            zone.addEventListener("drop", (event) => {
                event.preventDefault();
                const file = event.dataTransfer?.files?.[0];

                if (file) {
                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    input.files = transfer.files;
                    showFile(file);
                }
            });
        }
    });

    document.querySelectorAll("[data-toast]").forEach((toast) => {
        const dismiss = () => {
            toast.classList.add("is-leaving");
            setTimeout(() => toast.remove(), 220);
        };

        toast.querySelector("[data-toast-close]")?.addEventListener("click", dismiss);
        setTimeout(dismiss, 5200);
    });

    document.querySelectorAll("[data-favorite-form]").forEach((form) => {
        form.addEventListener("submit", async (event) => {
            event.preventDefault();

            const card = form.closest("[data-resource-card]");
            const count = card?.querySelector("[data-favorite-count]");
            const icon = card?.querySelector("[data-favorite-icon]");
            const label = form.querySelector("[data-favorite-label]");
            const previousCount = Number(count?.textContent || 0);
            const wasFavorited = icon?.getAttribute("fill") === "currentColor";
            const optimisticCount = Math.max(0, previousCount + (wasFavorited ? -1 : 1));

            if (count) count.textContent = String(optimisticCount);
            if (icon) {
                icon.setAttribute("fill", wasFavorited ? "none" : "currentColor");
                icon.classList.add("scale-125");
                setTimeout(() => icon.classList.remove("scale-125"), 220);
            }
            if (label) label.textContent = wasFavorited ? "Favoritar" : "Remover favorito";

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        Accept: "application/json",
                    },
                });

                if (!response.ok) throw new Error("favorite failed");
                const data = await response.json();

                if (count) count.textContent = String(data.favorites_count);
                if (icon) icon.setAttribute("fill", data.favorited ? "currentColor" : "none");
                if (label) label.textContent = data.favorited ? "Remover favorito" : "Favoritar";
                showToast(data.message, "success");
            } catch (error) {
                if (count) count.textContent = String(previousCount);
                if (icon) icon.setAttribute("fill", wasFavorited ? "currentColor" : "none");
                if (label) label.textContent = wasFavorited ? "Remover favorito" : "Favoritar";
                showToast("Não foi possível atualizar os favoritos.", "error");
            }
        });
    });

    document.querySelectorAll("[data-download-action]").forEach((link) => {
        link.addEventListener("click", () => {
            const card = link.closest("[data-resource-card]");
            const count = card?.querySelector("[data-download-count]");

            if (count) {
                count.textContent = String(Number(count.textContent || 0) + 1);
                count.classList.add("scale-125", "text-[#FE6807]");
                setTimeout(() => count.classList.remove("scale-125", "text-[#FE6807]"), 260);
            }

            showToast("Download iniciado.", "info");
        });
    });

    document.querySelectorAll("[data-notification-button]").forEach((button) => {
        const badge = button.querySelector("[data-notification-badge]");
        const key = button.dataset.notificationKey || "vutivi-notifications-read";

        if (localStorage.getItem(key) === "1" && badge) {
            badge.remove();
        }

        button.addEventListener("click", () => {
            localStorage.setItem(key, "1");
            if (badge) {
                badge.remove();
            }
        });
    });

    document.querySelectorAll("[data-copy-link]").forEach((button) => {
        button.addEventListener("click", async () => {
            const link = button.dataset.copyLink || window.location.href;
            await navigator.clipboard?.writeText(link);
            button.classList.add("bg-[#fff1e6]", "text-[#FE6807]");
            button.querySelector("[data-copy-label]").textContent = "Link copiado";
            setTimeout(() => {
                button.querySelector("[data-copy-label]").textContent = "Copiar link";
            }, 1800);
        });
    });

    document.querySelectorAll("[data-count-to]").forEach((counter) => {
        if (prefersReducedMotion.matches) {
            counter.textContent = counter.dataset.countTo;
            return;
        }

        const target = Number(counter.dataset.countTo || 0);
        let start = 0;
        const step = () => {
            start += Math.max(1, Math.ceil(target / 42));
            counter.textContent = String(Math.min(start, target));
            if (start < target) {
                requestAnimationFrame(step);
            }
        };
        step();
    });
});
