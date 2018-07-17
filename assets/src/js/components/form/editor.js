import FormView from './view'
import FormListSelect from './list-select'
import FormInactive from './inactive'

const { listsTotalItems } = wpChimpSettingState
const { BlockControls } = wp.editor
const { Component } = wp.element
const { Spinner, withAPIData } = wp.components

class FormEditor extends Component {
  render () {
    const { className, lists } = this.props
    const { isLoading, data } = lists

    if (isLoading || typeof data === 'undefined') {
      return (
        <div className={`${className} is-loading`}>
          <Spinner />
        </div>
      )
    }

    if (data.length >= 0) {
      return (
        <FormInactive className="wp-chimp-inactive" />
      )
    }

    return (
     <BlockControls key="form-controls" className={`${className}__block-controls`}>
        <FormListSelect {...this.props} />
        <FormView {...this.props} />
     </BlockControls>
    )
  }
}

export default withAPIData(() => {
  return {
    lists: `/wp-chimp/v1/lists?per_page=${listsTotalItems}`
  }
})(FormEditor)
