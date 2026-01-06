<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="text-xl font-semibold text-gray-800"><?php echo e($worker->name); ?>'s Profile</h2>
     <?php $__env->endSlot(); ?>

    <div class="max-w-3xl mx-auto py-8 space-y-4">
        <div class="bg-white p-6 rounded shadow">
            <?php if($worker->profile?->avatar): ?>
                <img src="<?php echo e(asset('storage/' . $worker->profile->avatar)); ?>" alt="Avatar" class="w-32 h-32 rounded-full mb-4">
            <?php endif; ?>

            <p><strong>Name:</strong> <?php echo e($worker->name); ?></p>
            <p><strong>Email:</strong> <?php echo e($worker->email); ?></p>
            <p><strong>Phone:</strong> <?php echo e($worker->profile->phone ?? '-'); ?></p>
            <p><strong>Skills:</strong> <?php echo e($worker->profile->skills ?? '-'); ?></p>
            <p><strong>Bio:</strong> <?php echo e($worker->profile->bio ?? '-'); ?></p>
            <p><strong>Joined:</strong> <?php echo e($worker->created_at->format('Y-m-d')); ?></p>
            
            <p><strong>Rating:</strong> <?php echo e(number_format($worker->profile->rating ?? 0, 1)); ?> / 5</p>
            <p><strong>Total Reviews:</strong> <?php echo e($worker->profile->total_reviews ?? 0); ?></p>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /Users/baikuili/Sites/job-platform/resources/views/workers/show.blade.php ENDPATH**/ ?>