const input = document.getElementById("searchInput");
const box = document.getElementById("suggestBox");

if (input) {

    input.addEventListener("keyup", () => {
        const keyword = input.value.trim();

        if (keyword.length < 1) {
            box.classList.add("hidden");
            return;
        }

        fetch("search_suggest.php?q=" + encodeURIComponent(keyword))
            .then(res => res.json())
            .then(data => {
                let html = "";

                if (data.length === 0) {
                    html = "<p class='p-3 text-gray-500'>Tidak ada hasil...</p>";
                } else {
                    data.forEach(item => {
                        html += `
                            <div class="p-3 hover:bg-blue-100 cursor-pointer"
                                 onclick="selectSuggestion('${item.title}')">
                                ${item.title}
                            </div>
                        `;
                    });
                }

                box.innerHTML = html;
                box.classList.remove("hidden");
            });
    });

    // Tutup box jika klik di luar
    document.addEventListener("click", e => {
        if (!box.contains(e.target) && e.target !== input) {
            box.classList.add("hidden");
        }
    });
}

function selectSuggestion(title) {
    input.value = title;      // isi input
    box.classList.add("hidden");
    input.form.submit();      // langsung jalankan pencarian
}
