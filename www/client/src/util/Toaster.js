import { Position, Toaster, Intent } from "@blueprintjs/core";
 
/** Singleton toaster instance. Create separate instances for different options. */
const AppToaster = Toaster.create({
    className: "lm-toaster",
    position: Position.TOP_RIGHT
});

export  default AppToaster;