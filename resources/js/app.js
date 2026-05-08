// Resources data will be populated from Laravel backend
let resources = [];

// Function to populate resources from DOM (Laravel data)
function populateResourcesFromDOM() {
    resources = [];
    document.querySelectorAll(".resource-card").forEach((card, index) => {
        const id = parseInt(card.dataset.id);
        const title = card.querySelector(".res-title").textContent;
        const type = card.dataset.type || "doc";
        const status = card.querySelector(".status-badge").textContent;
        const desc = card.querySelector(".res-desc").textContent;
        const metaItems = card.querySelectorAll(".res-meta span");

        let size = "N/A";
        let date = "N/A";
        let dateLabel = "Info";

        if (metaItems.length >= 2) {
            const metaText2 = metaItems[1].textContent.trim();
            size = metaText2;
        }

        if (metaItems.length >= 1) {
            const metaText1 = metaItems[0].textContent.trim();
            if (metaText1.includes("Dono:")) {
                dateLabel = "Proprietário";
                date = metaText1.replace("Dono:", "").trim();
            } else if (metaText1.includes("Local:")) {
                dateLabel = "Localização";
                date = metaText1.replace("Local:", "").trim();
            }
        }

        const iconClass = `icon-${type}`;

        resources.push({
            id: id,
            title: title,
            type: type.toUpperCase(),
            size: size,
            status: status,
            date: date,
            desc: desc,
            iconClass: iconClass,
            dateLabel: dateLabel,
        });
    });
}

let currentId = null;

function openModal(id) {
    currentId = id;
    const r = resources.find((res) => res.id === id);
    if (!r) return;
    document.getElementById("modalTitle").textContent = r.title;
    document.getElementById("modalResName").textContent = r.title;
    document.getElementById("modalResSub").textContent =
        r.type + " · " + r.size;

    const iconBox = document.getElementById("modalIconBox");
    iconBox.className = "modal-icon-box " + r.iconClass;
    iconBox.innerHTML = `<svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><span style="font-size:9px;font-weight:800;color:white;">${r.type}</span>`;

    const statusColor =
        r.status === "Disponível"
            ? "#22C55E"
            : r.status === "Em Uso"
              ? "#F59E0B"
              : "#EF4444";

    document.getElementById("modalInfoRow").innerHTML = `
        <div class="modal-info-item"><div class="modal-info-label">Estado</div><div class="modal-info-value" style="color:${statusColor}">${r.status}</div></div>
        <div class="modal-info-item"><div class="modal-info-label">Tamanho</div><div class="modal-info-value">${r.size}</div></div>
        <div class="modal-info-item"><div class="modal-info-label">${r.dateLabel}</div><div class="modal-info-value">${r.date}</div></div>
        <div class="modal-info-item"><div class="modal-info-label">Formato</div><div class="modal-info-value">${r.type}</div></div>
      `;

    document.getElementById("modalBtnAbrir").onclick = () => {
        closeModalDirect();
        showToast('Recurso "' + r.title + '" aberto!', "success");
    };
    document.getElementById("modalBtnEmprestar").onclick = () => {
        closeModalDirect();
        emprestar(currentId);
    };
    document.getElementById("modalBtnDevolver").onclick = () => {
        closeModalDirect();
        devolver(currentId);
    };

    document.getElementById("modalOverlay").classList.add("open");
}

function closeModal(e) {
    if (e.target === document.getElementById("modalOverlay"))
        closeModalDirect();
}
function closeModalDirect() {
    document.getElementById("modalOverlay").classList.remove("open");
}

function devolver(id) {
    const r = resources.find((res) => res.id === id);
    if (!r) return;

    showToast('Recurso "' + r.title + '" devolvido com sucesso.', "warning");
}

function emprestar(id) {
    const r = resources.find((res) => res.id === id);
    if (!r) return;

    // Send AJAX request to create reservation
    fetch("/reservations", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN":
                document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content") || "",
        },
        body: JSON.stringify({
            resource_id: id,
            // Add other reservation fields as needed
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showToast(
                    'Recurso "' + r.title + '" emprestado com sucesso!',
                    "success",
                );
                // Optionally reload the page or update the UI
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast(
                    "Erro ao emprestar recurso: " +
                        (data.message || "Erro desconhecido"),
                    "error",
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showToast("Erro ao emprestar recurso. Tente novamente.", "error");
        });
}

function showToast(msg, type) {
    const t = document.getElementById("toast");
    document.getElementById("toastMsg").textContent = msg;
    t.className = "toast " + type;
    t.classList.add("show");
    setTimeout(() => t.classList.remove("show"), 3200);
}

// Search
document.getElementById("searchInput").addEventListener("input", function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll(".resource-card").forEach((card) => {
        const title = card
            .querySelector(".res-title")
            .textContent.toLowerCase();
        card.style.opacity = title.includes(q) ? "1" : "0.3";
        card.style.pointerEvents = title.includes(q) ? "auto" : "none";
    });
});

// Nav
document.querySelectorAll(".type-list li a").forEach((link) => {
    link.addEventListener("click", (e) => {
        e.preventDefault();

        const filterType = link.dataset.filter;
        const option = document.querySelector(
            `.filter-option[data-filter="${filterType}"]`,
        );

        if (option) {
            option.click();
        }
    });
});

let currentFilter = "all";

function updateVisibleCards() {
    const cards = document.querySelectorAll('.resource-card');

    cards.forEach((card) => {
        const cardType = card.dataset.type;
        const shouldShow = currentFilter === 'all' || cardType === currentFilter;

        card.style.display = shouldShow ? 'block' : 'none';

        if (shouldShow) {
            card.style.opacity = '0';
            setTimeout(() => {
                card.style.opacity = '1';
            }, 50);
        }
    });
}

// Filter Dropdown
const filterBtn = document.getElementById("filterBtn");
const filterMenu = document.getElementById("filterMenu");
const filterText = document.getElementById("filterText");

// Toggle dropdown
filterBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    const isOpen = filterMenu.classList.contains("open");
    filterBtn.classList.toggle("open", !isOpen);
    filterMenu.classList.toggle("open", !isOpen);
});

// Close dropdown when clicking outside
document.addEventListener("click", (e) => {
    if (!filterBtn.contains(e.target) && !filterMenu.contains(e.target)) {
        filterBtn.classList.remove("open");
        filterMenu.classList.remove("open");
    }
});

// Filter options
const filterOptions = {
    all: { text: "Todas as Categorias", icon: "📁" },
    pdf: { text: "Documentos PDF", icon: "📄" },
    doc: { text: "Documentos Word", icon: "📝" },
    pptx: { text: "Apresentações", icon: "📊" },
    video: { text: "Vídeos", icon: "🎥" },
    xlsx: { text: "Planilhas", icon: "📈" },
    zip: { text: "Arquivos Compactados", icon: "📦" },
};

// Handle filter selection
document.querySelectorAll(".filter-option").forEach((option) => {
    option.addEventListener("click", () => {
        const filterType = option.dataset.filter;

        // Update active state
        document
            .querySelectorAll(".filter-option")
            .forEach((opt) => opt.classList.remove("active"));
        option.classList.add("active");

        // Update button text
        filterText.textContent = filterOptions[filterType].text;

        // Apply filter
        applyFilter(filterType);

        // Close dropdown
        filterBtn.classList.remove("open");
        filterMenu.classList.remove("open");
    });
});

// Apply filter function
function applyFilter(filterType) {
    currentFilter = filterType;
    updateVisibleCards();
}

// Add Resource Dropdown
const addResourceBtn = document.getElementById("addResourceBtn");
const addResourceMenu = document.getElementById("addResourceMenu");
const addResourceDropdown = document.querySelector(".add-resource-dropdown");

// Toggle add resource dropdown
if (addResourceBtn) {
    addResourceBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        const isOpen = addResourceDropdown.classList.contains("open");
        addResourceDropdown.classList.toggle("open", !isOpen);
    });
}

// Close add resource dropdown when clicking outside
document.addEventListener("click", (e) => {
    if (addResourceDropdown && !addResourceDropdown.contains(e.target)) {
        addResourceDropdown.classList.remove("open");
    }
});

// Initialize resources from DOM when page loads
document.addEventListener("DOMContentLoaded", function () {
    populateResourcesFromDOM();
    updateVisibleCards();
});
