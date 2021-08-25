import React, { Component } from 'react';
import { withRouter } from 'react-router-dom';

class ScrollTopView extends Component {
    componentDidUpdate(prevProps) {
      if (this.props.location !== prevProps.location) {
        // console.log('top...');
        window.scrollTo(0, 0);
      }
    }
  
    render() {
      return null;
    }
  }
  
export default ScrollTopView;