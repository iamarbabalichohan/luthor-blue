<?php
$key_ingredients = get_query_var('key_ingredients');
?>

<div class="flex justify-between">
    <h1 class="font-bold pt-4 py-2 sohne-400 capitalize">key ingredients</h1>
    <span class="pt-4 py-2 font-bold text-[14px]" id="toggleAccordion"><img
            src="<?php echo get_template_directory_uri() . "/assets/images/plus.png" ?>" alt="" srcset="">
    </span>

</div>
<div class="">
    <p id="accordionContent" class="text-gray-700 mt-2 transition-all duration-300 overflow-hidden">
        <span id="truncatedText"></span>
        <span id="fullText" class="hidden">
            <?php echo $key_ingredients; ?>
        </span>
    </p>
</div>