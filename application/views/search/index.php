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
            <?php else: ?>
                Ditemukan <?= count($results) ?> hasil untuk "<strong><?= htmlspecialchars($keyword) ?></strong>"
            <?php endif; ?>
        </p>

        <div class="fac-search-wrap" style="margin-bottom:24px">
            <form class="fac-search-form" action="<?= base_url('search') ?>" method="get">
                <i class="fas fa-search fac-search-icon"></i>
                <input type="text" name="q" class="fac-search-input"
                       placeholder="Cari nama dosen, NIP, atau keahlian..."
                       value="<?= htmlspecialchars($keyword) ?>"
                       autocomplete="off">
                <button type="submit" class="fac-search-btn">Cari</button>
            </form>
        </div>

        <?php if ($searched && empty($results)): ?>
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
        <?php endif; ?>
    </div>
</div>
