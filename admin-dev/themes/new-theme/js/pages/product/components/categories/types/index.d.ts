type Category = {
  id: number,
  name: string,
  displayName: string,
}

type TreeCategory = {
  id: number,
  name: string,
  displayName: string,
  active: boolean,
  children: Array<TreeCategory>,
}
