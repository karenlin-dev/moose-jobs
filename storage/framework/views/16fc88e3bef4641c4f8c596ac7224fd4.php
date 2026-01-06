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
            Worker Dashboard
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="max-w-7xl mx-auto py-8 grid grid-cols-1 md:grid-cols-3 gap-8">

        
        <div class="space-y-6">
            <div class="bg-white p-6 rounded shadow text-left">
                <?php if($user->profile?->avatar): ?>
                    <img src="<?php echo e(asset('storage/' . $user->profile->avatar)); ?>" alt="Avatar" class="w-32 h-32 rounded-full mx-auto mb-4">
                <?php else: ?>
                    <img src="<?php echo e(asset('images/default-avatar.png')); ?>" alt="Avatar" class="w-32 h-32 rounded-full mx-auto mb-4">
                <?php endif; ?>
                
                    <div class="flex flex-col justify-start">
                        <h2 class="text-xl font-bold"><?php echo e($user->name); ?></h2>
                        <p class="text-gray-600"><b>Email:</b> <?php echo e($user->email); ?></p>
                        <p class="text-gray-600"><b>City:</b><?php echo e($user->profile->city ?? 'Moose Jaw'); ?></p>
                        <p class="text-gray-600"><b>Phone:</b> <?php echo e($user->profile->phone ?? '-'); ?></p>
                        <p class="text-gray-600"><b>Skills:</b> <?php echo e($user->profile->skills ?? '-'); ?></p>
                        <p class="text-gray-600 mt-2"><b>Bio:</b> <?php echo e($user->profile->bio ?? '-'); ?></p>
                        <p class="text-gray-600"><b>Rating:</b> <?php echo e(number_format($user->profile->rating ?? 0, 1)); ?>/5</p>
                        <p class="text-gray-600 mt-2"><b>Total Reviews:</b> <?php echo e($user->profile->total_reviews ?? 0); ?></p>
                    </div>
                
                <a href="<?php echo e(route('workers.edit')); ?>"
                   class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                   Edit Profile
                </a>
            </div>
        </div>

        
        <div class="md:col-span-2 space-y-8">

            
            <?php if(session('success')): ?>
                <div class="bg-green-100 text-green-800 p-4 rounded">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            
            <div>
                <h3 class="text-lg font-semibold mb-2">Available Tasks</h3>

                <?php if($tasks->isEmpty()): ?>
                    <p class="text-gray-500">No tasks available to bid.</p>
                <?php else: ?>
                    <ul class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="py-2 flex justify-between items-center">
                                <div>
                                    <a href="<?php echo e(route('tasks.index', $task)); ?>" class="text-indigo-600 hover:underline font-medium">
                                        <?php echo e($task->title); ?>

                                    </a>
                                    <span class="text-gray-400 text-sm ml-2">
                                        Budget: $<?php echo e($task->budget); ?> · City: <?php echo e($task->city); ?>

                                    </span>
                                </div>

                                <?php
                                    $alreadyBid = $bids->pluck('job_id')->contains($task->id);
                                ?>

                                <?php if(!$alreadyBid): ?>
                                    <a href="<?php echo e(route('bids.create', $task)); ?>"
                                    class="text-white bg-indigo-600 px-4 py-1 rounded hover:bg-indigo-700">
                                        Submit Bid
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-500 px-4 py-1 rounded border">Already Bid</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>

            
            <div>
                <h3 class="text-lg font-semibold mb-2">My Bids</h3>

                <?php if($bids->isEmpty()): ?>
                    <p class="text-gray-500">You have not placed any bids yet.</p>
                <?php else: ?>
                    <ul class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $bids; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="py-2 flex justify-between items-center">
                            <div>
                            <?php if($bid->task): ?>
                                <a href="<?php echo e(route('bids.show', $bid)); ?>" class="text-indigo-600 hover:underline">
                                    <?php echo e($bid->task->title); ?>

                                </a>
                                <span class="text-gray-400 text-sm ml-2">
                                    <?php echo e($bid->status); ?> - <?php echo e($bid->created_at->diffForHumans()); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-gray-400 italic">Task has been deleted</span>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </ul>
                <?php endif; ?>
            </div>

            
            <div>
                <h3 class="text-lg font-semibold mb-2">My Assignments</h3>

                <?php if($assignments->isEmpty()): ?>
                    <p class="text-gray-500">You have no assignments yet.</p>
                <?php else: ?>
                    <ul class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="py-2 flex justify-between items-center">
                                <div>
                                    <a href="<?php echo e(route('assignments.show', $assignment)); ?>" class="text-indigo-600 hover:underline">
                                        <?php echo e($assignment->task->title); ?>

                                    </a>
                                    <span class="text-gray-400 text-sm ml-2">
                                        Started: <?php echo e($assignment->started_at->format('Y-m-d')); ?>

                                    </span>
                                </div>

                                <?php if($assignment->task->status !== 'completed'): ?>
                                    <form method="POST" action="<?php echo e(route('assignments.complete', $assignment)); ?>">
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
<?php $component->withAttributes([]); ?>Mark Completed <?php echo $__env->renderComponent(); ?>
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
                                <?php else: ?>
                                    <span class="text-green-600 font-semibold">Completed</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>

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
<?php /**PATH /Users/baikuili/Sites/job-platform/resources/views/components/dashboard/worker.blade.php ENDPATH**/ ?>