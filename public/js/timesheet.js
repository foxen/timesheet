/**
* @jsx React.DOM
*/
var Timesheet = React.createClass({
  getInitialState: function() {
    return {
      sideHeader: [['ZX','ZX'],['ZX','ZX'],['ZX','ZX']],
      header:[['ZX','ZX'],['ZX','ZX'],['ZX','ZX']],
      side:[['ZX','ZX'],['ZX','ZX'],['ZX','ZX']],
      body:[['ZX','ZX'],['ZX','ZX'],['ZX','ZX']],
    };
  },

  
  componentDidMount: function() {
    $.get(this.props.source, function(result) {

      this.setState({

        sideHeader: result.sh,
        header:result.h,
        side:result.s,
        body:result.b,

      });
    }.bind(this));
    
  },

  render: function() {
    return (
        <div className = "tt" >
        <div className = "sBar">
            <SideBar data = {this.state.side} offsetY = {this.state.offsetY}/>
        </div>
        <div className = "tmsh" >
            <Table data = {this.state.body}/>
        </div>
        </div>
    );
  }
});
//================================================================================
var Table = React.createClass({
  getInitialState: function() {
    return {data   :this.props.data,};
  },
  
  render: function () {
    return (
    <table><tbody>
        {this.props.data.map(function(row) {
            return (
                <tr className = "rw">
                    {$.map(row,function( val, index) {
                        return <td className="c">{val}</td>;
                    })}
                </tr>);
        })}
    </tbody></table>
    );
  }
});

var SideBar = React.createClass({
  
  getInitialState: function() {
    return {data   :this.props.data,
            offsetY:0,};
  },
  
  handleScroll:function(event){
    
    this.setState({
        dStile:{marginTop:-window.pageYOffset},
    });

  },

  componentDidMount: function() {
    window.addEventListener('scroll', this.handleScroll);
  },

  render: function () {
    return (
    <table style = {this.state.dStile} ><tbody>
        {this.props.data.map(function(row) {
            return (
                <tr className = "rw">
                    {$.map(row,function( val, index) {
                        return <td className="c">{val}</td>;
                    })}
                </tr>);
        })}
    </tbody></table>
    );
  }
});
//===============================================================================

React.renderComponent(
  <Timesheet source="http://127.0.0.1/timesheet/gettimesheet" />,
  document.getElementById('content'));