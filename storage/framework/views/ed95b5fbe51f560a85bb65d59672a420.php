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
        <h2 class="text-xl font-semibold text-gray-800">
            Available Tasks
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="max-w-5xl mx-auto py-8 space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white p-6 rounded shadow flex justify-between items-start">
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold">
                        <?php echo e($task->title); ?>

                    </h3>

                    <p class="text-sm text-gray-600">
                        <?php echo e($task->description); ?>

                    </p>

                    <p class="text-sm text-gray-500">
                        City: <?php echo e($task->city); ?> ·
                        Budget: $<?php echo e($task->budget); ?>

                    </p>

                    <?php if($task->category): ?>
                        <span class="inline-block text-xs bg-gray-100 px-2 py-1 rounded">
                            <?php echo e($task->category->name); ?>

                        </span>
                    <?php endif; ?>
                </div>

                
                <?php if(auth()->user()->role === 'worker'): ?>
                    <a href="<?php echo e(route('bids.create', $task)); ?>"
                       class="text-indigo-600 hover:underline mt-1">
                        Bid →
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-gray-500">No tasks available.</p>
        <?php endif; ?>
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
<?php /**PATH /Users/baikuili/Sites/job-platform/resources/views/tasks/index.blade.php ENDPATH**/ ?>