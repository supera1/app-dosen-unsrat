<div class="container">
    <div class="breadcrumb">
        <a href="<?= base_url() ?>">Beranda</a>
        <span class="sep">/</span>
        <strong>Pencarian Staf</strong>
    </div>
</div>

<div class="container">
    <div class="section" style="padding-top:0">
        <h2 class="section-title">Pencarian Staf</h2>
        <p style="color:var(--gray-500);margin-top:-16px;margin-bottom:24px">
            <?php if ($keyword === ''): ?>
                Cari dosen berdasarkan nama, NIP, atau keahlian.
            <?php elseif (!$searched): ?>
                Masukkan minimal 2 karakter.
            <?php elseif ($total === 0): ?>
                Tidak ada dosen ditemukan untuk "<strong><?= htmlspecialchars($keyword) ?></strong>".
            <?php else: ?>
                Menampilkan <?= (($page - 1) * $per_page) + 1 ?>&ndash;<?= min($page * $per_page, $total) ?>
                dari <?= $total ?> hasil untuk "<strong><?= htmlspecialchars($keyword) ?></strong>"
            <?php endif; ?>
        </p>

        <div class="fac-search-wrap" style="margin-bottom:24px">
            <form class="fac-search-form" action="<?= base_url('search') ?>" method="get" id="searchForm">
                <i class="fas fa-search fac-search-icon"></i>
                <input type="text" name="q" class="fac-search-input" id="searchInput"
                       placeholder="Cari nama dosen, NIP, atau keahlian..."
                       value="<?= htmlspecialchars($keyword) ?>"
                       autocomplete="off">
                <button type="submit" class="fac-search-btn">Cari</button>
            </form>
            <div id="searchDropdown" class="fac-dropdown" hidden>
                <ul class="fac-dropdown-list"></ul>
            </div>
        </div>

        <script>
        (function () {
            const input      = document.getElementById('searchInput');
            const dropdown   = document.getElementById('searchDropdown');
            const list       = dropdown.querySelector('.fac-dropdown-list');
            const suggestUrl = '<?= base_url('search/suggest') ?>';
            const staffBase  = '<?= base_url('staff/') ?>';
            let timer, active = -1;

            function initial(name) {
                return name.replace(/^(Prof\.|Dr\.|Ir\.|Drs\.|Dra\.)\s*/i, '').trim().charAt(0).toUpperCase();
            }

            function render(items) {
                list.textContent = '';

                if (!items.length) {
                    const empty = document.createElement('li');
                    empty.className = 'fac-dd-empty';
                    empty.textContent = 'Tidak ada hasil ditemukan';
                    list.appendChild(empty);
                } else {
                    items.forEach(function (s) {
                        const item = document.createElement('li');
                        item.className = 'fac-dd-item';

                        const link = document.createElement('a');
                        link.className = 'fac-dd-link';
                        link.href = staffBase + s.slug;

                        const avatar = document.createElement('span');
                        avatar.className = 'fac-dd-avatar';
                        avatar.textContent = initial(s.name);

                        const info = document.createElement('span');
                        info.className = 'fac-dd-info';

                        const name = document.createElement('span');
                        name.className = 'fac-dd-name';
                        name.textContent = s.name;

                        const dept = document.createElement('span');
                        dept.className = 'fac-dd-dept';
                        dept.textContent = s.department_name;

                        info.appendChild(name);
                        info.appendChild(dept);

                        const arrow = document.createElement('i');
                        arrow.className = 'fas fa-arrow-right fac-dd-arrow';

                        link.appendChild(avatar);
                        link.appendChild(info);
                        link.appendChild(arrow);
                        item.appendChild(link);
                        list.appendChild(item);
                    });
                }

                active = -1;
                dropdown.hidden = false;
            }

            function hide() {
                dropdown.hidden = true;
                active = -1;
            }

            function fetchSuggest(q) {
                fetch(suggestUrl + '?q=' + encodeURIComponent(q))
                    .then(function (r) { return r.json(); })
                    .then(render)
                    .catch(hide);
            }

            input.addEventListener('input', function () {
                clearTimeout(timer);
                const q = this.value.trim();
                if (q.length < 2) {
                    hide();
                    return;
                }
                timer = setTimeout(function () { fetchSuggest(q); }, 260);
            });

            input.addEventListener('keydown', function (e) {
                const items = list.querySelectorAll('.fac-dd-item');
                if (!items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    active = Math.min(active + 1, items.length - 1);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    active = Math.max(active - 1, 0);
                } else if (e.key === 'Enter' && active >= 0) {
                    e.preventDefault();
                    items[active].querySelector('a').click();
                    return;
                } else if (e.key === 'Escape') {
                    hide();
                    return;
                }

                items.forEach(function (el, i) {
                    el.classList.toggle('fac-dd-item--active', i === active);
                });
            });

            document.addEventListener('click', function (e) {
                if (!e.target.closest('.fac-search-wrap')) hide();
            });

            input.addEventListener('focus', function () {
                if (this.value.trim().length >= 2 && list.children.length) dropdown.hidden = false;
            });
        })();
        </script>

        <?php if ($searched && $total === 0): ?>
        <div class="empty-state">
            <i class="fas fa-users-slash"></i>
            <p>Tidak ada dosen ditemukan untuk "<?= htmlspecialchars($keyword) ?>".</p>
        </div>
        <?php elseif (!empty($results)): ?>
        <div class="staff-grid">
            <?php foreach ($results as $s): ?>
            <div class="staff-card">
                <div class="staff-avatar">
                    <?= strtoupper(substr($s->name, 0, 1)) ?>
                </div>
                <div class="info">
                    <h3><a href="<?= base_url('staff/' . $s->slug) ?>"><?= htmlspecialchars($s->full_title_name ?: $s->name) ?></a></h3>
                    <p class="dept"><?= htmlspecialchars($s->department_name) ?> &mdash; <?= htmlspecialchars($s->faculty_name) ?></p>
                    <?php if ($s->expertise): ?>
                    <span class="expertise"><?= htmlspecialchars($s->expertise) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?= $pagination ?>
        <?php endif; ?>
    </div>
</div>
