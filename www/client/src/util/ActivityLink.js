import React from "react";
import { BrowserRouter as Router, Route, Link } from "react-router-dom";
import { Icon } from "@blueprintjs/core";

const ActivityLink = ({ label, to, activeOnlyWhenExact, icon, color }) => (
    <Route
      path={to}
      exact={activeOnlyWhenExact}
      children={({ match }) => (
        <div className={match ? "active" : ""}>
         { icon && <Link to={to} className="lmlink"><Icon icon={icon} color={color} />&nbsp;{label}</Link> }
         { !icon && <Link to={to} className="lmlink">{label}</Link> }
          
        </div>
      )}
    />
  );

export default ActivityLink;  