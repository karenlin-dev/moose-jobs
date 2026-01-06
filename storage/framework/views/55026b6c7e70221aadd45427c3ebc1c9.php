<!DOCTYPE html>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(config('app.name', 'Moose Jobs')); ?></title>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- 导航栏 -->
        <?php echo $__env->make('layouts.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!-- 页面内容 -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <?php echo e($header ?? ''); ?>

            </div>
        </header>

        <main class="py-6">
            <?php echo e($slot); ?>

        </main>
    </div>
</body>
</html>
<?php /**PATH /Users/baikuili/Sites/job-platform/resources/views/layouts/app.blade.php ENDPATH**/ ?>