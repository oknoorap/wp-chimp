import { list, setChildren, el } from 'redom';

class TableRow {
  constructor() {
    this.el = el( 'tr' );
    this.locale = wpChimpLocaleAdmin;
  }
  update( data ) {
    setChildren( this.el, this.getData( data ) );
  }
  getData( data ) {
    return [
      el( 'td', [
        el( 'code', data.listId )
      ]),
      el( 'td', data.name ),
      el( 'td', data.subscribers ),
      el( 'td', ( 0 === data.doubleOptin ? this.locale.no : this.locale.yes ) ),
      el( 'td', [
        el( 'code', `[wp-chimp list_id="${data.listId}"]` )
      ])
    ];
  }
}

class TableBody {
  constructor() {
    this.el   = el( 'tbody', {
      'id': 'wp-chimp-table-lists-body'
    });
    this.list = list( this.el, TableRow );
    this.locale = wpChimpLocaleAdmin;
  }
  update( data ) {
    this.list.update( data );
  }
  showEmptyState() {
    var empty = [
      el( 'td', this.locale.noLists, {
        'colspan': '5'
      })
    ];
    setChildren( this.el, el( 'tr', empty ) );
  }
  showPlaceholder() {
    var placeholder = [];
    for ( let i = 0; 5 > i; i++ ) {
      placeholder.push( el( 'td', [
        el( 'span', '', {
          'class': `wp-chimp-table-data-placeholder__bar index-${i}`
        })
      ]) );
    }
    setChildren( this.el, el( 'tr', placeholder ) );
  }
}

export default TableBody;
