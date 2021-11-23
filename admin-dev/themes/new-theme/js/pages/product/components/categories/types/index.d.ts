type Category = {
  id: number,
  name: string,
}

type TypeaheadCategory = {
  id: number,
  name: string,
  breadcrumb: string,
}

type TreeCategory = {
  id: number,
  name: string,
  active: boolean,
  children: Array<TreeCategory>,
}
