/**
* @jsx React.DOM
*/
var Timesheet = React.createClass({
  getInitialState: function() {
    return {
      sideHeader: '',
      header:'',
      side:'',
      body:'',
    };
  },

  componentDidMount: function() {
    $.get(this.props.source, function(result) {

      this.setState({

        sideHeader: result['sh'],
        header:result['h'],
        side:result['s'],
        body:result['b'],

      });
    }.bind(this));
  },

  render: function() {
    return (
      <div>
        { this.state.side[0]}
      </div>
    );
  }
});


React.renderComponent(
  <Timesheet source="http://127.0.0.1/timesheet/gettimesheet" />,
  document.getElementById('content'));