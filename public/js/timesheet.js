/**
* @jsx React.DOM
*/
var DummyData = { fixed_head: [[' ',' '],[' ',' '],[' ',' ']],
                  head:        [[' ',' '],[' ',' '],[' ',' ']],
                  fixed:       [[' ',' '],[' ',' '],[' ',' ']],
                  body:        [[' ',' '],[' ',' '],[' ',' ']],}

var TimesheetTable = React.createClass({
  
  getInitialState: function() {
    return {
      data:    DummyData,
      loadingStyle:{display:'block',},
      isLoading: 1,
    };
  },
  
  handleMount: function(ch){
    console.log('m');
  },

  componentDidMount: function() {
    
    $.get(this.props.source, function(result) {
      this.setState({
        data: result,
        loadingStyle:{display:'none',},
        isLoading: 0,
      });
    }.bind(this));
    
  },

  render: function() {
    return (
      <div className = 'timesheet_table'>
      <loadingMark loadingStyle = {this.state.loadingStyle} 
            isLoading = {this.state.isLoading}/>
      
      <ContentTable data    = {this.state.data.fixed_head}  
                cName   = 'timesheet_fixed_head'/>                  
      
      <ContentTable data    = {this.state.data.head}  
                cName   = 'timesheet_head'
                scrollableX = 'X'/>

      <ContentTable data    = {this.state.data.body}
                handleMount = {this.handleMount.bind(this)} 
                cName   = 'timesheet_body'/>
      
      <ContentTable data    = {this.state.data.fixed}  
                scrollableY = 'Y'
                cName   = 'timesheet_fixed'/>
    
    </div>
    );
  }
});


//================================================================================

var ContentTable = React.createClass({
  getInitialState: function() {
    return {
      data:    this.props.data,
      scrollableX: 'none',
      scrollableY: 'none',   
    };
  },

  componentDidMount: function() {
    if(this.props.scrollableX == 'X') {
      window.addEventListener('scroll', this.handleScrollX);
    }
    if(this.props.scrollableY == 'Y') {
      window.addEventListener('scroll', this.handleScrollY);
    }
    
  },

  handleScrollY:function(event){
    
    this.setState({
        marginStyle:{marginTop:-window.pageYOffset},
    });

  },

  handleScrollX:function(event){
    
    this.setState({
        marginStyle:{marginLeft:-window.pageXOffset},
    });

  },

  render: function () {

    return (
    <table className = {this.props.cName} style = {this.state.marginStyle}>
      {$.map(this.props.data[0], function(val){
        return(
          <col className = {val.col_type}/>
        )
      })}
      <tbody>
        {$.map(this.props.data,function(row) {
            return (
                <tr>
                    {$.map(row,function(cell) {
                        var cx = React.addons.classSet; 
                        var classes = cx(cell.attributes);
                        return  <td>  
                                  <div id={cell.id} className = {classes}>
                                      {cell.value}
                                  </div>
                                </td>;
                    })}
                </tr>);
        })}
    </tbody></table>
    );
  }
});

var loadingMark = React.createClass({
  render: function(){
    return(
      <div className = "loadingMark" style = {this.props.loadingStyle}> 
        <div className = "innerLoading">
          обновляем данные...
        </div>


      </div>
    )
  }
});

//===============================================================================

React.renderComponent(
  <TimesheetTable source= {"gettimesheet"} />,
  document.getElementById('content'));
