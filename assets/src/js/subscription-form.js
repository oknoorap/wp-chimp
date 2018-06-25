/**
 * TODO: Phasing out jQuery.
 *
 * Make the code less reliant on jQuery.
 */
jQuery($ => {
  if ($('body').hasClass('wp-admin')) {
    return
  }

  /**
   * Get the translateable strings for the Admin Settings page.
   *
   * @type {Object}
   */
  const locale = wpChimpL10n

  /**
   * Display the notice on and off.
   *
   * @param noticeElem
   * @param noticeType
   */
  const toggelNotice = (noticeElem, response) => {
    let currentStatus
    let className
    let noticeMessage

    switch (response) {
      case 'success':
        className = 'wp-chimp-notice--success'
        noticeMessage = locale.subscribedNotice
        break

      case 'invalid_email':
        className = 'wp-chimp-notice--info'
        noticeMessage = locale.emailInvalidNotice
        break

      case 'error':
        className = 'wp-chimp-notice--error'
        noticeMessage = locale.errorNotice
        break
    }

    currentStatus = noticeElem.data('current-status')

    if (currentStatus) {
      noticeElem.removeClass(currentStatus)
    }

    if (className && noticeMessage) {
      noticeElem
        .text(noticeMessage)
        .addClass(`${className} is-displayed`)
        .data('current-status', className)
    }
  }

  $('body').on('submit', '.wp-chimp-form', event => {
    event.preventDefault()

    let form = $(event.currentTarget)
    let formData = form.serializeArray()
    let formParent = form.parents('.wp-chimp-subscription-form')
    let formFieldSet = form.children('.wp-chimp-form__fieldset')
    let formButton = formFieldSet.children('.wp-chimp-form__button')
    let formNotice = formParent.children('.wp-chimp-notice')
    let apiUrl = form.attr('action')

    $.ajax({
      type: 'POST',
      url: apiUrl,
      data: formData,
      beforeSend () {
        formParent.addClass('is-submitting').fadeTo(200, 0.5, () => {
          formFieldSet.prop('disabled', true)
          formButton.prop('disabled', true)
        })
      }
    })
      .always(() => {
        formParent.removeClass('is-submitting').fadeTo(200, 1, () => {
          formFieldSet.prop('disabled', false)
          formButton.prop('disabled', false)
        })
      })
      .done(response => {
        let status

        if (response.status === 'subscribed' || response.title === 'Member Exists') {
          status = 'success'
        }

        if (response.status === 'invalid_email') {
          status = 'invalid_email'
        }

        toggelNotice(formNotice, status)
      })
      .fail(() => toggelNotice(formNotice, 'error'))
  })
})
