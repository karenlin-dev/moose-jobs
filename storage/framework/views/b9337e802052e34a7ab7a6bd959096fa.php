     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center w-full">
            
            <?php if(auth()->user()->isEmployer()): ?>
                <a href="<?php echo e(route('tasks.create')); ?>"
                   class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Post Task
                </a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="max-w-6xl mx-auto py-8 space-y-8">
        
        <?php if(auth()->user()->isEmployer()): ?>
            <?php if (isset($component)) { $__componentOriginalb7446a54d33c52055b320f760b2bee8b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7446a54d33c52055b320f760b2bee8b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dashboard.employer','data' => ['tasks' => $tasks]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dashboard.employer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tasks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tasks)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7446a54d33c52055b320f760b2bee8b)): ?>
<?php $attributes = $__attributesOriginalb7446a54d33c52055b320f760b2bee8b; ?>
<?php unset($__attributesOriginalb7446a54d33c52055b320f760b2bee8b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7446a54d33c52055b320f760b2bee8b)): ?>
<?php $component = $__componentOriginalb7446a54d33c52055b320f760b2bee8b; ?>
<?php unset($__componentOriginalb7446a54d33c52055b320f760b2bee8b); ?>
<?php endif; ?>

        
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginala1e150c42c2d05b627f45ef63cc61ecb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala1e150c42c2d05b627f45ef63cc61ecb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dashboard.worker','data' => ['user' => $user,'tasks' => $tasks,'bids' => $bids,'assignments' => $assignments]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dashboard.worker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user),'tasks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tasks),'bids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bids),'assignments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($assignments)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala1e150c42c2d05b627f45ef63cc61ecb)): ?>
<?php $attributes = $__attributesOriginala1e150c42c2d05b627f45ef63cc61ecb; ?>
<?php unset($__attributesOriginala1e150c42c2d05b627f45ef63cc61ecb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala1e150c42c2d05b627f45ef63cc61ecb)): ?>
<?php $component = $__componentOriginala1e150c42c2d05b627f45ef63cc61ecb; ?>
<?php unset($__componentOriginala1e150c42c2d05b627f45ef63cc61ecb); ?>
<?php endif; ?>
        <?php endif; ?>
    </div>
<?php /**PATH /Users/baikuili/Sites/job-platform/resources/views/dashboard.blade.php ENDPATH**/ ?>