const { Component } = wp.element
const { Dashicon } = wp.components

class FormListSelect extends Component {
  render () {
    const { className, attributes, setAttributes, lists } = this.props
    const { list_id: listId } = attributes

    return (
      <div className={`${className}__select-list`} onChange={event => setAttributes({ list_id: event.target.value })}>
        <Dashicon icon="feedback" />
        <select value={listId}>
          {
            lists.data.map(({ list_id: listId, name }) => {
              return <option value={listId}>{name}</option>
            })
          }
        </select>
      </div>
    )
  }
}

export default FormListSelect
