import React from 'react';

class HelloMessage extends React.Component {
  render() {
    return (
      <div>
        Привет, {this.props.name}!
      </div>
    )
  }
}

export default HelloMessage;