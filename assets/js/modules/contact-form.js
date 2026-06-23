/**
 * Contact Form AJAX Handler
 *
 * Reads config from window.podnestContact (injected server-side by
 * PodNest_Contact::localize_config). Optionally fetches a reCAPTCHA v3
 * token before submitting if a site key is configured.
 *
 * @module modules/contact-form
 */

/**
 * Initialises AJAX submission for the contact form.
 *
 * @returns {void}
 */
export function initContactForm() {
    const form   = document.getElementById( 'pn-contact-form' );
    const config = window.podnestContact;

    if ( ! form || ! config ) {
        return;
    }

    const submitBtn  = document.getElementById( 'pn-contact-submit' );
    const labelEl    = submitBtn?.querySelector( '.pn-btn-label' );
    const spinnerEl  = submitBtn?.querySelector( '.pn-btn-spinner' );
    const errorEl    = document.getElementById( 'pn-contact-error' );
    const successEl  = document.getElementById( 'pn-contact-success' );
    const successMsg = document.getElementById( 'pn-contact-success-msg' );

    /** @param {boolean} loading */
    const setLoading = ( loading ) => {
        submitBtn.disabled = loading;
        labelEl?.toggleAttribute( 'hidden', loading );
        spinnerEl?.toggleAttribute( 'hidden', ! loading );
    };

    const hideError = () => errorEl?.setAttribute( 'hidden', '' );
    const showError = ( msg ) => {
        if ( ! errorEl ) return;
        errorEl.textContent = msg;
        errorEl.removeAttribute( 'hidden' );
    };

    form.addEventListener( 'submit', async ( e ) => {
        e.preventDefault();
        hideError();
        setLoading( true );

        try {
            const data = new FormData( form );
            data.append( 'action', 'podnest_submit_contact' );
            data.append( 'nonce',  config.nonce );

            /* Attach reCAPTCHA v3 token if configured */
            if ( config.recaptchaKey && window.grecaptcha ) {
                const token = await window.grecaptcha.execute(
                    config.recaptchaKey,
                    { action: 'contact' }
                );
                data.append( 'recaptcha_token', token );
            }

            const response = await fetch( config.ajaxUrl, { method: 'POST', body: data } );
            const json     = await response.json();

            if ( json.success ) {
                form.setAttribute( 'hidden', '' );
                if ( successEl && successMsg ) {
                    successMsg.textContent = json.data.message;
                    successEl.removeAttribute( 'hidden' );
                    successEl.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
                }
            } else {
                showError( json.data?.message ?? 'An error occurred. Please try again.' );
            }
        } catch {
            showError( 'Network error. Please check your connection and try again.' );
        } finally {
            setLoading( false );
        }
    } );
}