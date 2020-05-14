import React from 'react';
import axios from 'axios';
import { Link } from 'react-router-dom';

function Navbar({ auth, setAuth }) {
  const onLogout = async () => {
    try {
      const { data } = await axios.get('/auth/logout');
      console.log(data);
      setAuth(null);
    } catch (err) {
      console.error(err);
    }
  }
  return (
    <ul className="nav">
      <li className="nav-item">
        <Link to="/">Home</Link>
      </li>
      {
        auth.isTrainer && (
          <li className="nav-item">
            <Link to="/create-session">Create session</Link>
          </li>
        )
      }
      <li className="nav-item">
        <a href="#" onClick={onLogout}>Logout</a>
      </li>
    </ul>
  )
}

export default Navbar;