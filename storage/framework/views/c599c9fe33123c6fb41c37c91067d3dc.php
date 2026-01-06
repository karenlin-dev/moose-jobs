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
        <div class="flex justify-between items-center">
            <?php if(auth()->user()->isEmployer()): ?>
                <a href="<?php echo e(route('tasks.create')); ?>"
                   class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Post Task
                </a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="max-w-5xl mx-auto py-8 space-y-6">
        <h2 class="text-xl font-semibold text-gray-800">
            <?php echo e(auth()->user()->isEmployer() ? 'My Tasks' : 'Worker Dashboard'); ?>

        </h2>

        <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white p-6 rounded shadow space-y-2">
                <h3 class="text-lg font-semibold"><?php echo e($task->title); ?></h3>
                <p class="text-gray-600"><?php echo e($task->description); ?></p>
                <p class="text-sm text-gray-500">
                    Budget: $<?php echo e($task->budget); ?> | Status: <?php echo e($task->status); ?>

                </p>

                <?php if($task->bids->count() > 0): ?>
                    <div class="mt-2">
                        <h4 class="font-semibold">Bids:</h4>
                        <ul class="divide-y divide-gray-200">
                            <?php $__currentLoopData = $task->bids; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isAccepted = $bid->status === 'accepted';
                                ?>

                                <li class="py-2 flex justify-between items-center <?php echo e($isAccepted ? 'bg-green-100 rounded p-2' : ''); ?>">
                                    <div>
                                        <span class="font-medium"><?php echo e($bid->worker->name); ?></span> -
                                        $<?php echo e($bid->price); ?>

                                        <span class="text-gray-400 text-sm">(<?php echo e($bid->status); ?>)</span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="<?php echo e(route('workers.show', $bid->worker)); ?>"
                                           class="text-indigo-600 hover:underline">View Profile</a>

                                        <?php if($bid->status === 'pending' && $task->status === 'open'): ?>
                                            <form class="accept-bid-form" method="POST" action="<?php echo e(route('bids.accept', $bid)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Accept <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">No bids yet.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-gray-500">You have not posted any tasks yet.</p>
        <?php endif; ?>
    </div>

    <script>
    document.querySelectorAll('.accept-bid-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = form.action;
            const data = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(url, {
                method: 'PATCH',
                body: data,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(json => {
                // 找到这个 bid 的 li 元素
                const li = form.closest('li');
                li.classList.add('bg-green-100', 'rounded', 'p-2');

                // 更新状态文本
                const statusSpan = li.querySelector('span.text-gray-400');
                if(statusSpan) statusSpan.textContent = '(accepted)';

                // 移除 Accept 按钮
                form.remove();

                alert(json.message);
            })
            .catch(err => console.error(err));
        });
    });
    </script>
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
<?php /**PATH /Users/baikuili/Sites/job-platform/resources/views/components/dashboard/employer.blade.php ENDPATH**/ ?>