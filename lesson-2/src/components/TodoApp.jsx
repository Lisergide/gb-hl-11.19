import React from 'react';
import axios from 'axios';
import TodoList from "./TodoList";

class TodoApp extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      items: [],
      text: ''
    };
  }

  handleChange = (e) => {
    this.setState({text: e.target.value});
  };

  handleSubmit = (e) => {
    e.preventDefault();
    axios.get('http://acloud-centos:3034/api/hello', {
      headers: {
        'Access-Control-Allow-Origin': 'http://acloud-centos',
      },
    });
    if (!this.state.text.length) {
      return;
    }
    const newItem = {
      text: this.state.text,
      id: Date.now()
    };
    this.setState(state => ({
      items: state.items.concat(newItem),
      text: ''
    }));
  };

  render() {
    return (
      <div>
        <h3>Список дел</h3>
        <TodoList items={this.state.items}/>
        <form onSubmit={this.handleSubmit}>
          <label htmlFor="new-todo">
            Что нужно сделать?
          </label> <br/>
          <input type="text"
                 id="new-todo"
                 onChange={this.handleChange}
                 value={this.state.text}/>
          <button>
            Добавить #{this.state.items.length + 1}
          </button>
        </form>
      </div>
    );
  }
}


export default TodoApp;