<div class="custom-form">
    <form class="custom-form__form" id="customForm" method="post" action="">
        <div class="custom-form__field">
            <label class="custom-form__label" for="cfp_name">Name:</label>
            <input class="custom-form__input" type="text" id="cfp_name" name="cfp_name" required>
        </div>

        <div class="custom-form__field">
            <label class="custom-form__label" for="cfp_email">Email:</label>
            <input class="custom-form__input" type="email" id="cfp_email" name="cfp_email" required>
        </div>

        <div class="custom-form__field">
            <label class="custom-form__label" for="cfp_phone">Phone:</label>
            <input class="custom-form__input" type="tel" id="cfp_phone" name="cfp_phone" required pattern="^\+44[1-9]\d{9}$" title="Please enter a valid UK phone number.">
        </div>

        <div class="custom-form__field">
            <label class="custom-form__label" for="cfp_postcode">Postal Code:</label>
            <input class="custom-form__input" type="text" id="cfp_postcode" name="cfp_postcode" required pattern="^[A-Z]{1,2}\d[A-Z\d]? ?\d[A-Z]{2}$" title="Please enter a valid UK postal code.">
        </div>

        <!-- Include nonce for security -->
        <input type="hidden" name="cfp_nonce" value="<?php echo wp_create_nonce('cfp_form_nonce'); ?>">

        <button class="custom-form__button" id="submitFormButton" type="submit">Contact</button>
        <div id="loader" class="d-none">Loading...</div> <!-- Loader for AJAX requests -->
    </form>
    <div class="custom-form__response" id="formResponse"></div>
</div>
