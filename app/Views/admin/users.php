<?= $this->extend('partials/header') ?>
<?= $this->section('content') ?>
<div class="p-margin-desktop">
    <h2 class="font-headline-md text-headline-md text-primary mb-6">Validation des comptes</h2>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-4"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (empty($users)): ?>
        <p class="text-on-surface-variant">Aucun compte en attente de validation.</p>
    <?php else: ?>
        <div class="bg-surface-container-lowest rounded-xl shadow border border-outline-variant/30 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low border-b">
                    <tr>
                        <th class="p-4 font-label-md">Nom</th>
                        <th class="p-4 font-label-md">Date d'inscription</th>
                        <th class="p-4 font-label-md text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr class="border-b hover:bg-surface-container-low/50">
                        <td class="p-4"><?= esc($user['nom']) ?></td>
                        <td class="p-4"><?= $user['created_at'] ?></td>
                        <td class="p-4 text-right">
                            <a href="/admin/validate/<?= $user['id'] ?>" class="bg-primary text-on-primary px-4 py-1.5 rounded-lg hover:bg-primary-container transition-colors">Valider</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>